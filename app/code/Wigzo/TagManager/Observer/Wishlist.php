<?php
namespace Wigzo\TagManager\Observer;

use \Magento\Framework\Event\ObserverInterface;

class Wishlist implements ObserverInterface {
    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager,\Magento\Framework\ObjectManagerInterface $objectManager)
    {
         $this->_objectManager = $objectManager;
         $this->_storeManager=$storeManager;
    }
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $scopeInterface = $this->_objectManager->create ('\Magento\Framework\App\Config\ScopeConfigInterface');
		$enabled = $scopeInterface->getValue ("admin/wigzo/enabled");
        var_dump($enabled);
        if (null == $enabled || $enabled == "false") {
		    return;
        }
		$wigzo_host = $scopeInterface->getValue ("admin/wigzo/host");//"https://flash.wigzopush.com";
		if($wigzo_host == "" || NULL == $wigzo_host)
		{
			$wigzo_host = "https://app.wigzo.com";
		}
	  if (file_exists("/tmp/wigzomode")) {
            $wigzo_host = trim(file_get_contents("/tmp/wigzomode"));
        }
		$cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
		$cookieID = $cookieManager->getCookie('WIGZO_LEARNER_ID');		
		$pageUuid = $cookieManager->getCookie('PAGE_UUID');
		$orgToken = $scopeInterface->getValue ("admin/wigzo/orgId");
		$lang = $scopeInterface->getValue ("general/locale/code");
		$timestamp = date('Y-m-d H:i:s');
        $eventCategory = "EXTERNAL";
        $source = "web";
        $obj = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
        $ip =  base64_encode($obj->getRemoteAddress());
        $productId = $observer->getEvent()->getProduct()->getId();
        $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        $productName = $currentproduct->getName(); 
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $productPrice = $currentproduct->getPrice();
        $formattedPrice = $priceHelper->currency($productPrice, true, false);
        $this->_header = $objectManager->get('Magento\\Framework\\HTTP\\Header');
		$userAgent = $this->_header->getHttpUserAgent();
        $canonicalUrl = $currentproduct->getProductUrl();
        $store = $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $currentproduct->getImage();
        $description = $currentproduct->getData('description');
                
        $postevent = array();
        $postevent['productId']=$productId;
        $postevent['title']=$productName;
        $postevent['productPrice']=$formattedPrice;
        $postevent['canonicalUrl']=$canonicalUrl;
        $postevent['lang']=$lang; 
        $postevent['eventCategory']=$eventCategory;
        $postevent['imageUrl']=$imageUrl;
        $postevent['source']=$source;
        $postevent['mRemoteAddress']= $ip;
        $postevent['description']= $description;
       
        $postdata = array();
        $postdata['productId']=$productId;
        $postdata['title']=$productName;
        $postdata['canonicalUrl']=$canonicalUrl;
        $postdata['productPrice']=$formattedPrice;
        $postdata['lang']=$lang; 
        $postdata['eventCategory']=$eventCategory;
        $postdata['_']=$timestamp;
        $postdata['e']="";
        $postdata['eventval']=$postevent;
        $postdata['source']=$source;
        $postdata['mRemoteAddress']= $ip;
        $postevent['imageUrl']=$imageUrl;
        $postevent['description']= $description;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $wigzo_host."/learn/" . $orgToken . "/wishlist/" . $cookieID."?_siteid=".$orgToken);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,1); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); //timeout in seconds
        $server_output = curl_exec($ch);
        
        if($server_output == false)
        {
            return;
        }
        else
        {
            $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($response == 200) {
                curl_close($ch);
            }
            else
            {
                return;
            }
        }
            
    }
}