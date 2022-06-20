<?php 
namespace Evincemage\AdminOrderStatus\Controller\Orders;  

class UpdateStatus extends \Magento\Framework\App\Action\Action {

protected $resultFactory;
protected $scopeConfig;
protected $_orderCollectionFactory;
protected $orderRepository;
protected $order;
protected $_curl;

public function __construct(
    \Magento\Framework\Controller\ResultFactory $resultFactory,
    \Magento\Framework\App\Action\Context $context,
	\Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
	\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
	\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
	\Magento\Sales\Model\OrderFactory $order,
	\Magento\Framework\HTTP\Client\Curl $curl
)
{
   $this->resultFactory = $resultFactory;
   $this->scopeConfig = $scopeConfig;
   $this->_orderCollectionFactory = $orderCollectionFactory;
   $this->orderRepository = $orderRepository;
   $this->order = $order;
   $this->_curl = $curl;
   parent::__construct($context);
}

public function execute() { 
		/*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/paytm_transaction.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info(__METHOD__);*/
		
        $collection = $this->_orderCollectionFactory->create()
         ->addAttributeToSelect('*')->addFieldToFilter('status',['null' => true]);
		 
		$order = $collection->getData();
	
		echo "<pre>";
		print_r($order);
		//die();
		if(count($order) != 0){
          foreach($order as $orderdata){
			$orderId = $orderdata['entity_id'];
			$order = $this->orderRepository->get($orderId);
			//$order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
			if($orderdata['state'] == 'new'){
				$order->setStatus('pending');
			}else{
				$order->setStatus($orderdata['state']);
			}
			try {
				$this->orderRepository->save($order);
			} catch (\Exception $e) {
				$this->logger->error($e);
				$this->messageManager->addExceptionMessage($e, $e->getMessage());
			}				
		  }
		}	   
    }
} 
?>
