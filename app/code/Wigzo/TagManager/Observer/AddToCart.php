<?php
namespace Wigzo\TagManager\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\Bootstrap;
use \Magento\Framework\ObjectManagerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class AddToCart implements ObserverInterface {

    /** @var CheckoutSession */
    private $checkoutSession;
	private $_header;
    /**
     * @param CheckoutSession $checkoutSession
     */

    public function __construct(CheckoutSession $checkoutSession,
                                \Magento\Framework\ObjectManagerInterface $objectManager,
                                \Magento\Framework\HTTP\Adapter\Curl $httpAdapter,
                                \Magento\Framework\HTTP\Adapter\CurlFactory $curlFactory,
                                \Magento\Framework\Json\Helper\Data $jsonHelper,
                                PsrLoggerInterface $logger
    ) {
        $this->_httpAdapter = $httpAdapter;
        $this->curlFactory = $curlFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;

    }

    private function _postEvent($postUrl, $postData){
        try {
            $http = $this->curlFactory->create();
            $config = ['timeout' => 2, 'useragent' => $this->_header->getHttpUserAgent()];
            $http->setConfig($config);
            $http->write(\Zend_Http_Client::POST, $postUrl, '1.1',
                [ "Content-Type:application/json", "Authorization: Basic cm9iaW5sYXVAcG9zdG5ldC5jb20uYXU6TWFnaWNtYW4yMTEwNzU=" ], //http_build_query($postParams, '', '&'),
                json_encode($postData));
            $result = $http->read();
            $body = \Zend_Http_Response::extractBody($result);
            $res_code = \Zend_Http_Response::extractCode($result);
            if (!empty($body)){/* convert JSON to Array */ $this->logger->info(json_encode($body)); }
            else if (empty($body)) { $this->logger->info("empty body unable to decode"); }
        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->logger->info(json_encode($debugData));
            throw $e;
        }
        finally {
            $http->close();
        }
    }



    /*
    * POST CURL API
    */
    public function callPostAPI($url, $requstbody) {
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info("callPostAPI inovoked with $url :" .$url. " and $requstbody: " .$requstbody);

        /* Create curl factory */
        $httpAdapter = $this->curlFactory->create();
        /* Forth parameter is POST body */
        $httpAdapter->write(\Zend_Http_Client::POST, $url, '1.1', ["Content-Type:application/json","Authorization: Basic cm9iaW5sYXVAcG9zdG5ldC5jb20uYXU6TWFnaWNtYW4yMTEwNzU="],json_encode($requstbody));
        $result = $httpAdapter->read();
        $body = \Zend_Http_Response::extractBody($result);
        /* convert JSON to Array */
        $response = $this->jsonHelper->jsonDecode($body);
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info($response);
    }




    public function execute(Observer $observer) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $cartModel = $objectManager->create('Magento\Checkout\Model\Cart');
		$productsObject = $cartModel->getQuote()->getAllVisibleItems();
		
		$scopeInterface = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
		$enabled = $scopeInterface->getValue("admin/wigzo/enabled");
		if ($enabled == NULL || $enabled == "false") {
          
        }
		$wigzo_host = $scopeInterface->getValue ("admin/wigzo/host");
		if($wigzo_host == "" || NULL == $wigzo_host)
		{
			$wigzo_host = "https://app.wigzo.com";
		}
	  if (file_exists("/tmp/wigzomode")) {
            $wigzo_host = trim(file_get_contents("/tmp/wigzomode"));
        }
		$cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
		$cookieID = $cookieManager->getCookie('WIGZO_LEARNER_ID');
        if($cookieID == null || $cookieID =="")
		{
		}
		$pageUuid = $cookieManager->getCookie('PAGE_UUID');
        if($pageUuid == null || $pageUuid =="")
		{
		}
		$orgToken = $scopeInterface->getValue ("admin/wigzo/orgId");
		$lang = $scopeInterface->getValue ("general/locale/code");
		$timestamp = date('Y-m-d H:i:s');
        $eventCategory = "EXTERNAL";
        $source = "web";
		$cartItems = array();
		if($productsObject == null || $productsObject =="")
		{
		}
		else
		{
        foreach ($productsObject as $item) {
					$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
					$product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
					$store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
					$imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
					$obj = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
       				$ip =  base64_encode($obj->getRemoteAddress());
					$priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
					$price = $item->getPrice();
					$formattedPrice = $priceHelper->currency($price, true, false);
					$this->_header = $objectManager->get('Magento\\Framework\\HTTP\\Header');
					$userAgent = $this->_header->getHttpUserAgent();
					\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(json_encode($item));
					
					if (null != $currentproduct) {
						//Post data to be sent in the request.   
                        $postData = ["canonical"=>$currentproduct->getProductUrl(),
                            "name"=>$item->getName(),"productId"=>$item->getId(),"title"=>$item->getName(),"image"=>$imageUrl,
                            "lang"=>$lang, "eventCategory"=>$eventCategory, "_"=>$timestamp,"e"=>"","pageuuid"=>$pageUuid,
                            "eventval"=>'{"canonicalUrl":'.'"'.$currentproduct->getProductUrl().'"'.',"title":'.'"'.$item->getName().'"'.',"description": '.'"'.$item->getData('description').'"'.',"price": '.'"'.$formattedPrice.'"'.',"productId": '.'"'.$item->getProductId().'"'.',"image": '.'"'.$imageUrl.'"'.',"Category": '.'"'.$eventCategory.'"'.',"language": '.'"'.$lang.'"'.',"mRemoteAddress": '.'"'.$ip.'"'.'}',
                            "source"=>$source,"price"=>$formattedPrice,"mRemoteAddress"=> $ip];

//                        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->info(json_encode($postdata));

                        $postUrl = $wigzo_host."/learn/" . $orgToken . "/addtocart/" . $cookieID."?u=".$currentproduct->getProductUrl()."?u=".$currentproduct->getProductUrl()."&_siteid=".$orgToken;


                        $this->_postEvent($postUrl, $postData);
					} else {
						return;
					}
			}	
		}
    }
}