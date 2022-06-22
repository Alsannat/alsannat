<?php
namespace Lotus\SMSAShipment\Controller\Adminhtml\Shipment;

use WebGate\SMSAShipping\Helper\Data;
use WebGate\SMSAShipping\Helper\SMSA;
use Magento\Sales\Model\Order\Address\Renderer;
use WebGate\SMSAShipping\Model\SmsashippinglogsFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\ObjectManagerInterface;

class Create extends \Magento\Backend\App\Action
{
    /**
     * @var SmsashippinglogsFactory
     */
    private $smsashippinglogsFactory;
    /**
     * @var SMSA
     */
    private $SMSA;
    private $dataHelper;
    /**
     * @var Renderer
     */
    private $addressRenderer;
    
    /**
     * The OrderRepository is used to load, save and delete orders.
     *
     * @var \Magento\Sales\Model\OrderRepository
     */
    protected $orderRepository;

    /**
     * The ShipmentFactory is used to create a new Shipment.
     *
     * @var Order\ShipmentFactory
     */
    protected $shipmentFactory;

    /**
     * The ShipmentRepository is used to load, save and delete shipments.
     *
     * @var Order\ShipmentRepository
     */
    protected $shipmentRepository;

    /**
     * The ShipmentNotifier class is used to send a notification email to the customer.
     *
     * @var ShipmentNotifier
     */
    protected $shipmentNotifier;
    protected $_messageManager;
    protected $resultFactory;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $trackingFactory;
    protected $object_manager;
    /**
     * All specified parameters in the constructor will be injected via dependency injection.
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ObjectManagerInterface $object_manager,
        SmsashippinglogsFactory $smsashippinglogsFactory,
        SMSA $SMSA,
        Data $dataHelper,
        Renderer $addressRenderer,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory
    ) {
        parent::__construct($context);

        $this->orderRepository = $orderRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->_messageManager = $messageManager;
        $this->object_manager = $object_manager;
        $this->smsashippinglogsFactory = $smsashippinglogsFactory;
        $this->SMSA = $SMSA;
        $this->dataHelper = $dataHelper;
        $this->addressRenderer = $addressRenderer;
        $this->trackFactory = $trackFactory;
        $this->resultFactory = $resultFactory;
        
    }

    /**
     * Since our class extends the Magento Action class it *must* have an execute() function.
     * This function is automatically called as soon as the request is sent.
     * @return void
     */
    public function execute()
    {   
        $orderId = $this->getRequest()->getParam('order_id', 0);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        
        if ($orderId <= 0) {
            $this->_messageManager->addError('Order not found.');
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;
        }

        try {
            $order = $this->orderRepository->get($orderId);

            if ($order == null) {
                $this->_messageManager->addError('Order not loaded from database.');
                $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
                return $resultRedirect;
            }

            $this->addShipmentMPS($order);

            if($order->getData('awd_number')){
                $awd_number = $order->getData('awd_number');
                $this->createShipment($order , $awd_number);
            }else{
                $this->_messageManager->addError('Cannot create shipment SMSA for the order.Read SMSA Shipping Logs for more information.');
            }

            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;

        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;
        }
    }

    /**
     * Creates a new shipment for the specified order.
     *
     * @param \Magento\Sales\Model\Order $order
     */
    protected function createShipment($order , $trackingNumber)
    {

        $storeId = $order->getStoreId();
        $carrierTitle = $this->dataHelper->getConfigValue('title','carriers/smsashipping/', $storeId);
        $data = array(
            'carrier_code' => 'smsashipping',
            'title' => $carrierTitle,
            'number' => $trackingNumber,
        );

        if ($order->canShip()) {
            $convertOrder = $this->object_manager->create('Magento\Sales\Model\Convert\Order');
            $shipment = $convertOrder->toShipment($order);
            
            foreach ($order->getAllItems() AS $orderItem) {
                if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                $shipment->addItem($shipmentItem);
            }
            
            // Register shipment
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);

            try {
                $track = $this->trackFactory->create()->addData($data);
                $shipment->addTrack($track)->save();
                
                $shipment->save();
                $shipment->getOrder()->save();
        
                // Send email
                //$this->shipmentNotifier->notify($shipment);
                $shipment->save();
                $this->_messageManager->addSuccess('Create Shipment Succesfully.');
            } catch (\Exception $e) {
                $this->_messageManager->addError('Shipment Not Created '.$e->getMessage());
            }
        } else {
            $shipmentCollection = $order->getShipmentsCollection();
            $shipment = $shipmentCollection->getFirstItem();

            $tracksCollection = $order->getTracksCollection();
            $addTrack = false;
            
            foreach ($tracksCollection->getItems() as $track) {
                if($track->getTrackNumber() == $data['number'] || $track->getCarrierCode() == $data['carrier_code']){
                    $addTrack = true;
                }
            }

            if(!$addTrack){
                try {
                    $track = $this->trackFactory->create()->addData($data);
                    $shipment->addTrack($track)->save();
                    $shipment->getOrder()->save();

                    $this->_messageManager->addSuccess('Add SMSA Shipment Succesfully.');
                } catch (\Exception $e) {
                    $this->_messageManager->addError('Shipment Not Created '.$e->getMessage());
                }
            }else{
                $this->_messageManager->addError('Shipment Already exists');
            }
        }
    }

    public function addShipmentMPS($order)
    {
$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/snaptec_haonguyen.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info('Your text message');
        $address = $order->getShippingAddress();
        $customer_name = $address->getFirstname() . ' ' . $address->getLastname();
        $full_address = $this->addressRenderer->format($address , 'text');
        $order_items = $order->getAllVisibleItems();
        $productweight = 0;
        $description = '';
        $count_items = 0;
        $refno = $order->getIncrementId();
        foreach ($order_items as $product) {
            $productweight += ($product->getWeight() * $product->getQtyOrdered()) ;
            if($count_items > 0){
                $description .= ', '.$product->getSku();
            }else{
                $description .= $product->getSku();
            }
            $count_items++;
        }
        $city = $address->getCity() ? $address->getCity() : $address->getRegion();
        $passKey = $this->dataHelper->getPassKey();
        $data = [
            'passKey' => $passKey ,
            'refNo' => (string) $refno ,
            'sentDate' => date('Y-m-d h:i:s a') ,
            'idNo' => (string) $order->getId() ,
            'cName' => (string) $customer_name ,
            'cntry' => (string) $address->getCountryId() , // 'Riyadh'
            'cCity' => (string) $city ,
            'cZip' => (string) $address->getPostcode() ,
            'cPOBox' => (string) $address->getPostcode() ,
            'cMobile' => (string) $address->getTelephone() ,
            'cTel1' => (string) $address->getTelephone() ,
            'cTel2' => '' ,
            'cAddr1' => $full_address ,
            'cAddr2' => '' ,
            'shipType' => 'DLV' ,
            'PCs' => (string) $order->getTotalItemCount() ,
            'cEmail' => (string) $address->getEmail() ,
            'carrValue' => '' ,
            'carrCurr' => (string) $order->getOrderCurrency()->getCurrencySymbol() ,
            'codAmt' => (string) $order->getPayment()->getMethod() == 'cashondelivery' ? $order->getTotalDue() : '0' ,
            'weight' => (string) $productweight ,
            'custVal' => '' ,
            'custCurr' => (string) $order->getOrderCurrencyCode() ,
            'insrAmt' => '' ,
            'insrCurr' => '' ,
            'itemDesc' => (string) $description ,
            'sName' => (string) $this->dataHelper->getShipperName() ,
            'sContact' => (string) $this->dataHelper->getShipperContact() ,
            'sAddr1' => (string) $this->dataHelper->getShipperAddress() ,
            'sAddr2' => '' ,
            'sCity' => (string) $this->dataHelper->getShipperCity() ,
            'sPhone' => (string) $this->dataHelper->getShipperPhone() ,
            'sCntry' => (string) $this->dataHelper->getShipperCountry() ,
            'prefDelvDate' => '' ,
            'gpsPoints' => '' ,
        ];

        $log = $this->smsashippinglogsFactory->create();
        $log_data = [
            'order_id' => $order->getId() ,
            'customer_id' => $order->getShippingAddress()->getCustomerId() ,
            'customer_name' => $customer_name ,
        ];
        // send data soap
        $SMSA = $this->SMSA->addShipMPS($data);

        if($SMSA instanceof Exception)
        {
            // submit log
            $log_data['response'] = $SMSA->getMessage();
            $log_data['awd_status'] = 'error';
            $log->setData($log_data)->save();
        }else{
            // set awd_number order
            if(isset($SMSA->addShipMPSResult)){
                
                if (strpos($SMSA->addShipMPSResult, 'Failed') !== false) {
                    $log_data['awd_status'] = 'error';
                }else{
                    $results = explode(",",$SMSA->addShipMPSResult);
                    $awd_number = $results[0];
                    $awd_status = $this->SMSA->getStatus($awd_number);

                    if($awd_status instanceof Exception)
                    {
                        $log_data['awd_status'] = $awd_status->getMessage();
                    }else{
                        $log_data['awd_status'] = $awd_status;
                    }
                    
                    $order->addData([
                        'awd_number' => $awd_number ,
                        'awd_status' => ($awd_status instanceof Exception) ? '' : $awd_status ,
                    ])->save();

                    $shipmentStatus = $this->SMSA->getStatus($awd_number);;
                    $logger->info('get Shipment Stautus:');
                    $logger->info(print_r($shipmentStatus,true));
                }
            }
            
            // submit log
            $log_data['response'] = $SMSA->addShipMPSResult;
            $log->setData($log_data)->save();
            
            return $order;
        }
    }
}

?>