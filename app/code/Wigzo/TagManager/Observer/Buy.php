<?php
namespace Wigzo\TagManager\Observer;
 
use Magento\Framework\ObjectManager\ObjectManager;
class Buy implements \Magento\Framework\Event\ObserverInterface {
 
    /** @var \Magento\Framework\Logger\Monolog */
    private $_logger;
    /**
     * @var \Magento\Framework\ObjectManager\ObjectManager
     */
    private $_objectManager;
    private $_header;
    private $_orderFactory;
    
    public function __construct(\Magento\Checkout\Model\Cart $cart,\Magento\Checkout\Model\Session $checkoutSession,\Magento\Framework\HTTP\Adapter\Curl $httpAdapter,/*CheckoutSession $checkoutSession,*/\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,\Magento\Store\Model\StoreManagerInterface $storeManager,\Magento\Framework\ObjectManagerInterface $objectManager,\Magento\Sales\Model\OrderFactory $orderFactory,\Psr\Log\LoggerInterface $logger /*log injection*/)
    {
        $this->_httpAdapter = $httpAdapter;
        $this->_objectManager = $objectManager;
        $this->_storeManager=$storeManager;
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->logger = $logger;								
    }	
    public function execute(\Magento\Framework\Event\Observer $observer ) {        
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
		$scopeInterface = $this->_objectManager->create ('\Magento\Framework\App\Config\ScopeConfigInterface');
		$cartModel = $objectManager->create('Magento\Checkout\Model\Cart');
		$productsObject = $cartModel->getQuote()->getAllVisibleItems();
		$enabled = $scopeInterface->getValue ("admin/wigzo/enabled");
		$obj = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
       	$ip =  base64_encode($obj->getRemoteAddress());
		if ($enabled == NULL || $enabled == "false") {
		    return;
        }
        if (file_exists("/tmp/wigzomode")) {
            $wigzo_host = trim(file_get_contents("/tmp/wigzomode"));
        }
		$wigzo_host = $scopeInterface->getValue ("admin/wigzo/host");
		if($wigzo_host == "" || NULL == $wigzo_host)
		{
			$wigzo_host = "https://app.wigzo.com";
		}
		$cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
		$cookieID = $cookieManager->getCookie('WIGZO_LEARNER_ID');
		if($cookieID == NULL || $cookieID == "")
		{
			return;
		}
		$pageUuid = $cookieManager->getCookie('PAGE_UUID');
		if($pageUuid == NULL || $pageUuid == "")
		{
			return;
		}
		$orgToken = $scopeInterface->getValue ("admin/wigzo/orgId");
		if($orgToken == NULL || $orgToken == "")
		{
			return;
		}	
		$lang = $scopeInterface->getValue ("general/locale/code");
		if($lang == NULL || $lang == "")
		{
			return;
		}	
		$timestamp = date('Y-m-d H:i:s');
        $eventCategory = "EXTERNAL";
        $source = "web";
		$resource = $cookieManager->getCookie('core/resource');
		$resourcecon = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resourcecon->getConnection();
		$tableName = $resourcecon->getTableName('sales_order_item'); 
		$this->_header = $objectManager->get('Magento\\Framework\\HTTP\\Header');
		$userAgent = $this->_header->getHttpUserAgent();
		/************************Cart Items*******************************************/
        $orderIds = $observer->getEvent()->getOrderIds();       
		$orderObject = $objectManager->get('\Magento\Sales\Model\Order');
		$orderId = $orderIds[0];
		if($orderId == NULL || $orderId == "")
		{
			return;
		}	
		$query =  "SELECT `product_id` FROM `" . $tableName . "` WHERE `order_id` = ". (int)$orderId;
		$product_id = $connection->fetchAll($query);
		
		$newevent = array();
		if($product_id !== NULL)
		{
				$eventVal = array();
				$i = 0;
				foreach($product_id as $temp)
				{
					$eventVal[$i] = $temp;
					$i++;
				}
				$cnt = count($eventVal);		
				for($j =0; $j<$cnt; $j++)
				{				
					$newevent [$j] = $eventVal[$j]['product_id'];	
				}
		}
        $postdata = ["lang"=>$lang, "eventCategory"=>$eventCategory, "_"=>$timestamp,"e"=>"","pageuuid"=>$pageUuid,"eventval"=>$newevent, "source"=>$source,"mRemoteAddress"=> $ip];
       
        $config= array('timeout' => 4);
        $this->_httpAdapter->setConfig($config);
        $this->_httpAdapter->addOption(CURLOPT_RETURNTRANSFER, 1);
        $this->_httpAdapter->addOption(CURLOPT_HEADER,0);
        $this->_httpAdapter->addOption(CURLOPT_POST,1);
        $this->_httpAdapter->addOption(CURLOPT_USERAGENT, $userAgent);
        $this->_httpAdapter->write(\Zend_Http_Client::POST, $wigzo_host."/learn/" . $orgToken . "/buy/" . $cookieID."?_siteid=".$orgToken,
        '1.1',array("Authorization: Basic cm9iaW5sYXVAcG9zdG5ldC5jb20uYXU6TWFnaWNtYW4yMTEwNzU=",
         "Content-Type: application/json"), json_encode($postdata));  // $data is an array which I need to convert in jeson and send.
        
        $server_output = $this->_httpAdapter->read();
		if($server_output == false)
		{
            return;
		} else {
           if($server_output === 200) {
                $this->_httpAdapter->close();
            } else {
                return;
                }
        }
	}
}
