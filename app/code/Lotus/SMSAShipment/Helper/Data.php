<?php
    
namespace Lotus\SMSAShipment\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use WebGate\SMSAShipping\Helper\Data as WebGateHelperData;
use WebGate\SMSAShipping\Helper\SMSA;
use Magento\Sales\Model\Order\Address\Renderer;
use WebGate\SMSAShipping\Model\SmsashippinglogsFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class Data extends AbstractHelper
{
    const XML_PATH_CONFIGBASE = 'Trackemail/smsa_track_email/';
    /**
     * @var SmsashippinglogsFactory
     */
    private $smsashippinglogsFactory;
    /**
     * @var SMSA
     */
    private $SMSA;
    private $webGateHelperData;
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
     * Object of \Magento\Framework\Controller\Result\JsonFactory
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    protected $_storeManager;
    private $_transportBuilder;
    protected $inlineTranslation;
    protected $vnescomsSmsHelper;
    /**
     * Object of \Magento\Sales\Model\Order
     * @var \Magento\Sales\Model\Order
     */
    private $modelOrder;
    /**
     * All specified parameters in the constructor will be injected via dependency injection.
     *
     * @param \Magento\Backend\App\Action\Context $context
     */
    /**
     * Data constructor.
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Sales\Model\Order\ShipmentRepository $shipmentRepository,
        \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ObjectManagerInterface $object_manager,
        SmsashippinglogsFactory $smsashippinglogsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order $modelOrder,
        SMSA $SMSA,
        \Vnecoms\Sms\Helper\Data $vnescomsSmsHelper,
        WebGateHelperData $webGateHelperData,
        Renderer $addressRenderer,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
        StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $_storeManager
    ) {

        parent::__construct($context);

        $this->orderRepository = $orderRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->_messageManager = $messageManager;
        $this->object_manager = $object_manager;
        $this->smsashippinglogsFactory = $smsashippinglogsFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelOrder = $modelOrder;
        $this->SMSA = $SMSA;
        $this->vnescomsSmsHelper = $vnescomsSmsHelper;
        $this->webGateHelperData = $webGateHelperData;
        $this->addressRenderer = $addressRenderer;
        $this->trackFactory = $trackFactory;
        $this->resultFactory = $resultFactory;
        $this->_transportBuilder = $_transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->_storeManager = $_storeManager;
    }

    public function getSMSAShipmentsStatus($orderId, $showmess = null) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        
        if ($orderId <= 0) {
            $this->_messageManager->addError('Order not found.');
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;
        }

        try {
            $order = $this->orderRepository->get($orderId);
            if ($order == null) {
                if($showmess){
                    $this->_messageManager->addError('Order not loaded from database.');
                }
                
                $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
                //return $resultRedirect;
                return $this;
            }
            $smsaTrackNum = $this->getSMSAShipmentsByOrderId($order);
            
            if($order->getData('awd_number') || $smsaTrackNum){
                $awd_number_a = explode(",", $smsaTrackNum);
                $awd_status_fn = '';
                foreach ($awd_number_a as $key => $awd_num) {
                    $showErr = true;
                    $awd_status = $this->SMSA->getStatus($awd_num,$showErr);

                    if($awd_status && $awd_status == 'PROOF OF DELIVERY CAPTURED'){
                        $awd_status = 'DELIVERED';
                    }

                    if($key == 0){
                        $awd_status_fn .= $awd_status;
                    }else{
                        $awd_status_fn .= ', '.$awd_status;
                    }
                }
                $order->setData('awd_number' , $smsaTrackNum);
                $order->setData('awd_status' , $awd_status_fn);
                $order->save();
                if($showmess){
                    $this->_messageManager->addSuccess(__('Status of Shipment Number %1 is %2', $awd_number, $awd_status));
                }
            }else{
                if($showmess){
                    $this->_messageManager->addError('The SMSA Shipment not exit.');
                }
            }
            
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            //return $resultRedirect;
            return $this;

        } catch (\Exception $exception) {
            if($showmess){
                $this->_messageManager->addError($exception->getMessage());
            }
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            //return $resultRedirect;
            return $this;
        }
    }

    public function getSMSAShipmentsByOrderId($order) {
        $trackNum = '';
        $shipment = $order->getShipmentsCollection()->getFirstItem();
        $count = 0;
        foreach ($shipment->getAllTracks() as $_item){
            if($_item->getTitle() == 'SMSA.COM' || $_item->getCarrierCode() == 'smsashipping'){
                
                if($count == 0){
                    $trackNum = $_item->getNumber();
                }else{
                    $trackNum .= ',' . $_item->getNumber();
                }
                $count ++;
            }
        }

        return $trackNum;
    }

    public function sendTrackEmail($order,$toEmail)
    {
        $templateVars = [
            'order'=> $order,
            'name' => $this->getCustomerName($order),
            'status' => $order->getData('awd_status'),
            'orderId' =>'#'.$order->getIncrementId(),
            'tracknumber' => $this->getSMSAShipmentsByOrderId($order),
        ];
        $data = [
            'name' => $this->getCustomerName($order),
            'status' => $order->getData('awd_status'),
            'orderId' =>'#'.$order->getIncrementId(),
            'tracknumber' => $this->getSMSAShipmentsByOrderId($order),
        ];

        $fromEmail = $this->scopeConfig->getValue('trackemail/smsa_track_email/identity', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->inlineTranslation->suspend();
        if ($fromEmail == null) {
            $fromEmail = "general";
        }

        $store = $this->_storeManager->getStore()->getId();
        $templateOptions = [
          'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
          'store' => $store
        ];
        $templateIdentifier = $this->scopeConfig->getValue('trackemail/smsa_track_email/template', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try{
            $transport = $this->_transportBuilder->setTemplateIdentifier($templateIdentifier)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($fromEmail)
                ->addTo($toEmail)            
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
        }
    }

    public function sendTrackBySMS($order,$message)
    {
        if(!$this->vnescomsSmsHelper->getCurrentGateway()) return;
        
        $additionalData = '';
        $tracknumber = $this->getSMSAShipmentsByOrderId($order);
        /* Send notification message to customer when a new shipment is created*/
        $customer = $this->vnescomsSmsHelper->getCustomerObjectForSendingSms($order);
        $message = str_replace("{{name}}",$this->getCustomerName($order),$message);
        $message = str_replace("{{track_status}}",$order->getData('awd_status'),$message);
        $message = str_replace("{{tracknumber}}",$tracknumber,$message);
        $message = str_replace("{{orderid}}",'#'.$order->getIncrementId(),$message);
        $this->vnescomsSmsHelper->sendCustomerSms($customer, $message, $additionalData);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE
        );
    }

    public function getTrackEmail($storeId = null)
    {
        return $this->getConfigValue('trackemail/smsa_track_email/active', $storeId);
    }

    public function getTrackEmailContent($storeId = null)
    {
        return $this->getConfigValue('trackemail/smsa_track_email/email_comment', $storeId);
    }

    public function getTrackSMSContent($storeId = null)
    {
        return $this->getConfigValue('trackemail/smsa_track_email/otp_message', $storeId);
    }

    public function getCustomerName(\Magento\Sales\Model\Order $order)
    {
        $customeName = ($order->getCustomerFirstname()) ? $order->getCustomerFirstname() : $order->getBillingAddress()->getFirstName();
        return $customeName;
    }
    
}
