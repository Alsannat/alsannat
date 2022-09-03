<?php

namespace Wigzo\TagManager\Block\ScriptTag;


class Index extends \Magento\Framework\View\Element\Template {

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Config\Model\ResourceModel\Config $resourceConfig,\Magento\Framework\ObjectManagerInterface $objectManager,
	//	 \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,\Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
	 array $data = [])
    {
		$om = \Magento\Framework\App\ObjectManager::getInstance();
		 $customerSession = $om->get('Magento\Customer\Model\Session');
		/*$this->_cacheTypeList = $cacheTypeList;
		$this->_cacheFrontendPool = $cacheFrontendPool;
		$this->clearcache();
		*/
        $scopeInterface = $context->getScopeConfig();

        $data["enabled"] = false;
        if ($scopeInterface->getValue ("admin/wigzo/enabled") == "true") {
            $data["enabled"] = true;
        }

        $data["browserpush"] = false;
        if ($scopeInterface->getValue ("admin/wigzo/browserpush") == "true") {
            $data["browserpush"] = true;
        }

        $data["onsite"] = false;
        if ($scopeInterface->getValue ("admin/wigzo/onsitepush") == "true") {
            $data["onsite"] = true;
        }

        $data["viahttps"] = false;
        if ($scopeInterface->getValue ("admin/wigzo/viahttps") == "true") {
            $data["viahttps"] = true;
        }

        $data["subpath"] = "";
        $data["userIdentifier"] = $customerSession->getCustomer()->getEmail();
        $data["orgidentifier"] = $scopeInterface->getValue ("admin/wigzo/orgId");

        $data["standardhost"] = true;
        $data["host"] = "https://app.wigzo.com";
        $data["tracker"] = "https://tracker.wigzopush.com";

        parent::__construct($context, $data);
		
    }
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
	/*public function getUrl($route = '', $params = [])
    {
        return $this->_urlBuilder->getUrl($route, $params);
    }
*/
	public function getCurrentUrl() {
    		return $this->_storeManager->getStore()->getCurrentUrl();
	}
	
	public function getCartItems()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $cartModel = $objectManager->create('Magento\Checkout\Model\Cart');
        $productsObject = $cartModel->getQuote()->getAllVisibleItems();
        
        $cartItems = array();
        foreach ($productsObject as $item) {
			
			$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
			
            $cartItems[] = $item;
			$itemInfo = array();
			$itemInfo["id"] =$item->getProductId();
			$itemInfo["name"]= $item->getName();
			$itemInfo["price"] = $item->getPrice();
			$itemInfo["imageurl"] = $currentproduct->getProductUrl().'pub/media/catalog/product'.$item->getImage();
			$itemInfo["producturl"] = $currentproduct->getProductUrl();
			return $itemInfo;
        }
        
        
    }
}
