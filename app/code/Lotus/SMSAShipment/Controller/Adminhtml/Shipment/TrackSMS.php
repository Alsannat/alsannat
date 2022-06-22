<?php
namespace Lotus\SMSAShipment\Controller\Adminhtml\Shipment;

use WebGate\SMSAShipping\Helper\Data;
use WebGate\SMSAShipping\Helper\SMSA;
use Magento\Sales\Model\Order\Address\Renderer;
use WebGate\SMSAShipping\Model\SmsashippinglogsFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\ObjectManagerInterface;

class TrackSMS extends \Magento\Backend\App\Action
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
     * Object of \Magento\Framework\Controller\Result\JsonFactory
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * Object of \Magento\Sales\Model\Order
     * @var \Magento\Sales\Model\Order
     */
    private $modelOrder;
    protected $ltSMSAhelper;
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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order $modelOrder,
        SMSA $SMSA,
        Data $dataHelper,
        \Lotus\SMSAShipment\Helper\Data $ltSMSAhelper,
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
        $this->resultJsonFactory = $resultJsonFactory;
        $this->modelOrder = $modelOrder;
        $this->SMSA = $SMSA;
        $this->dataHelper = $dataHelper;
        $this->ltSMSAhelper = $ltSMSAhelper;
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
        $orderId = $this->getRequest()->getParam('order_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {
            $order = $this->orderRepository->get($orderId);

            if ($orderId <= 0) {
                $this->_messageManager->addError('Order not found.');
                $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
                return $resultRedirect;
            }
            if(!$this->ltSMSAhelper->getSMSAShipmentsByOrderId($order)){
                $this->_messageManager->addError('Order not have SMSA Shippment.');
                $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
                return $resultRedirect;
            }
            $mess = $this->ltSMSAhelper->getTrackSMSContent();
            
            $this->ltSMSAhelper->sendTrackBySMS($order,$mess);
            $this->_messageManager->addSuccess(__('The Track SMS has been sent successfully'));
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;
        } catch (\Exception $exception) {
            $this->_messageManager->addError($exception->getMessage());
            $resultRedirect->setPath('sales/order/view',['order_id'=>$orderId]);
            return $resultRedirect;
        }
    }
}

?>