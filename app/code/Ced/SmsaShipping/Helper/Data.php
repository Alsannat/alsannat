<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_SmsaShipping
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */ 

namespace Ced\SmsaShipping\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    const VORDER_CREATE   = "VORDER_CREATE";  
    const VORDER_CANCELED   = "VORDER_CANCELED";
    const VORDER_PAYMENT_STATE_CHANGED   = "VORDER_PAYMENT_STATE_CHANGED";
    
    const SALES_ORDER_CREATE   = "SALES_ORDER_CREATE";  
    const SALES_ORDER_CANCELED   = "SALES_ORDER_CANCELED";  
    const SALES_ORDER_ITEM   = "SALES_ORDER_ITEM";  
    const SALES_ORDER_PAYMENT_STATE_CHANGED   = "SALES_ORDER_PAYMENT_STATE_CHANGED";  
    
    const VPAYMENT_CREATE   = "VPAYMENT_CREATE";  
    const VPAYMENT_TOTAL_AMOUNT   = "VPAYMENT_TOTAL_AMOUNT";  
    
    protected $_allowedFeedType = array();
    protected $_objectManager;
    protected $_storeManager;
    protected $_scopeConfigManager;
    protected $_configValueManager;
    protected $_transaction;
    protected $_context;
    protected $_cacheTypeList;
    protected $_cacheFrontendPool;
    protected $request;
    protected $_productMetadata;
    /* protected $_assetRepo; */
    protected $_storeId = 0;
    
    
    public function __construct(\Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {

        $this->_objectManager = $objectManager;
        $this->_context = $context;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_request = $request;
        $this->_productMetadata = $productMetadata;
        parent::__construct($context);
        $this->_storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $this->_scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->_configValueManager = $this->_objectManager->get('Magento\Framework\App\Config\ValueInterface');
        $this->_transaction = $this->_objectManager->get('Magento\Framework\DB\Transaction');
    }
    
    /**
     * Set a specified store ID value
     *
     * @param  int $store
     * @return $this
     */
    public function setStoreId($store)
    {
        $this->_storeId = $store;
        return $this;
    }
    
    public function getCustomCSS()
    {
        return $this->_scopeConfigManager->getValue('ced_csmarketplace/vendor/theme_css', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
    
    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsDashboard()
    {
        return $this->getVendorUrl() == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'/index' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'/index/' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'index' == $this->_getUrl('*/*/*')
        ||
        $this->getVendorUrl().'index/' == $this->_getUrl('*/*/*');
    }

    public function setLogo($logo_src, $logo_alt)
    {
        $this->setLogoSrc($logo_src);
        $this->setLogoAlt($logo_alt);
        return $this;
    }

    public function getLogoSrc()
    {    
        $logo_path = $this->_scopeConfigManager->getValue('ced_csmarketplace/vendor/vendor_logo_src', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
        return $logo_path;
    }

    public function getLogoAlt()
    {    
        return $this->_scopeConfigManager->getValue('ced_csmarketplace/vendor/vendor_logo_alt', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
    
    public function getVendorFooterText()
    {    
        return $this->_scopeConfigManager->getValue('ced_csmarketplace/vendor/vendor_footer_text', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStore()->getId());
    }
    
    public function getMarketplaceVersion()
    {
        return trim((string)$this->getReleaseVersion('Ced_CsMarketplace'));
    }
    
    public function getReleaseVersion($module)
    {
        $modulePath = $this->moduleRegistry->getPath(self::XML_PATH_INSTALLATED_MODULES, $module);
        $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, "$modulePath/etc/module.xml");
        $source = new \Magento\Framework\Simplexml\Config($filePath);
        if($source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version) {
            return $source->getNode(self::XML_PATH_INSTALLATED_MODULES)->attributes()->release_version->__toString(); 
        }
        return false; 
    }
   
    
    /**
     * Url encode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function prepareParams($param_data)
    {
        if(!is_array($param_data) && strlen($param_data)) {
            return urlencode($param_data);
        }
        if($param_data && is_array($param_data) && count($param_data)>0) {
            foreach($param_data as $key=>$value){
                $param_data[$key] = urlencode($value);
            }
            return $param_data;
        }
        return false;
    }
    
    /**
     * Url decode the parameters
     *
     * @param  string | array
     * @return string | array | boolean
     */
    public function extractParams($param_data)
    {
        if(!is_array($param_data) && strlen($param_data)) {
            return urldecode($param_data);
        }
        if($param_data && is_array($param_data) && count($param_data)>0) {
            foreach($param_data as $key=>$value){
                $param_data[$key] = urldecode($value);
            }
            return $param_data;
        }
        return false;
    }
    
    /**
     * Add params into url string
     *
     * @param  string  $url       (default '')
     * @param  array   $params    (default array())
     * @param  boolean $urlencode (default true)
     * @return string | array
     */
    public function addParams($url = '', $params = array(), $urlencode = true) 
    {
        if(count($params)>0) {
            foreach($params as $key=>$value){
                if(parse_url($url, PHP_URL_QUERY)) {
                    if($urlencode) {
                        $url .= '&'.$key.'='.$this->prepareParams($value); 
                    }
                    else {
                        $url .= '&'.$key.'='.$value; 
                    }
                } else {
                    if($urlencode) {
                        $url .= '?'.$key.'='.$this->prepareParams($value); 
                    }
                    else {
                        $url .= '?'.$key.'='.$value; 
                    }
                }
            }
        }
        return $url;
    }
    
    /**
     * Retrieve all the extensions name and version developed by CedCommerce
     *
     * @param  boolean $asString (default false)
     * @return array|string
     */
    public function getCedCommerceExtensions($asString = false) 
    {
        if($asString) {
            $cedCommerceModules = '';
        } else {
            $cedCommerceModules = array();
        }
        $allModules = $this->_context->getScopeConfig()->getValue(\Ced\SmsaShipping\Model\Feed::XML_PATH_INSTALLATED_MODULES);
        $allModules = json_decode(json_encode($allModules), true);
        foreach($allModules as $name=>$module) {
            $name = trim($name);
            if(preg_match('/ced_/i', $name) && isset($module['release_version'])) {
                if($asString) {
                    $cedCommerceModules .= $name.':'.trim($module['release_version']).'~';
                } else {
                    $cedCommerceModules[$name] = trim($module['release_version']);
                }
            }
        }
        if($asString) { trim($cedCommerceModules, '~'); 
        }
        return $cedCommerceModules;
    }
    
    /**
     * Retrieve environment information of magento
     * And installed extensions provided by CedCommerce
     *
     * @return array
     */
    public function getEnvironmentInformation() 
    {
        $info = array();
        $info['domain_name'] = $this->_productMetadata->getBaseUrl();
        $info['magento_edition'] = 'default';
        if(method_exists('Mage', 'getEdition')) { $info['magento_edition'] = $this->_productMetadata->getEdition(); 
        }
        $info['magento_version'] = $this->_productMetadata->getVersion();
        $info['php_version'] = phpversion();
        $info['feed_types'] = $this->getStoreConfig(\Ced\SmsaShipping\Model\Feed::XML_FEED_TYPES);
        $info['installed_extensions_by_cedcommerce'] = $this->getCedCommerceExtensions(true);
        
        return $info;
    }
  
    /**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getCsMarketplaceUrl()
    {
        return $this->_getUrl('csmarketplace/vshops');
    }
    

    /**
     * Retrieve CsMarketplace title
     *
     * @return string
     */
    public function getCsMarketplaceTitle()
    {
        return $this->getStoreConfig('ced_vshops/general/vshoppage_top_title', $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null)->getId());
    }
    
    /**
     * Retrieve I am a Vendor title
     *
     * @return string
     */
    public function getIAmAVendorTitle()
    {
        return $this->getStoreConfig('ced_vshops/general/vshoppage_title');
    }
    
    /**
     * Check customer account sharing is enabled
     *
     * @return boolean
     */
    public function isSharingEnabled()
    {
        if($this->scopeConfig->getValue(\Magento\Customer\Model\Config\Share::XML_PATH_CUSTOMER_ACCOUNT_SHARE) == \Magento\Customer\Model\Config\Share::SHARE_GLOBAL) {
            return true; 
        }
        return false;
    }
    
    /**
     * get Product limit
     *
     * @return integer
     */
    public function getVendorProductLimit()
    {
        return $this->scopeConfig->getValue('ced_vproducts/general/limit');
    }
    
    /**
     * Retrieve vendor account page url
     *
     * @return string
     */
    public function getVendorUrl()
    {
        return $this->_getUrl('csmarketplace/vendor');
    }
    
    /**
     * Authenticate vendor
     *
     * @param  int $customerId
     * @return boolean
     */
    public function authenticate($customerId = 0) 
    {
        if ($customerId) {
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByCustomerId($customerId);
            if($vendor && $vendor->getId()) {
                return $this->canShow($vendor);
            }
        }
        return false;
    }
    
    /**
     * Check if a vendor can be shown
     *
     * @param  Ced_CsMarketplace_Model_Vendor|int $vendor
     * @return boolean
     */
    public function canShow($vendor) 
    {
        if (is_numeric($vendor)) {
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->load($vendor);
        }
        
        if (!is_object($vendor)) {
            $vendor = $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->loadByAttribute('shop_url', $vendor);
        }

        if (!$vendor || !$vendor->getId()) {
            return false;
        }

        if (!$vendor->getIsActive()) {
            return false;
        }
        if($this->_objectManager->get('Magento\Framework\App\State')->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            if(!$this->isSharingEnabled() && ($vendor->getWebsiteId() != $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getWebsiteId())) {
                return false;
            }
        }

        return true;
    }
    

    /**
     *Rebuild Website Ids
     *
     *@param  $vendor
     * @return array $websiteIds
     */
    public function rebuildWebsites()
    {
        $collection = $this->_objectManager->get('Ced\CsMarketplace\Model\Vproducts')->getVendorProducts('', 0, 0, -1)->setOrder('vendor_id', 'ASC');
        foreach ($collection as $row){
            $productIds[]=$row->getProductId();
        }
        $previousVendorId=0;
        $vendorWebsiteIds=array();
        $removeWebsiteIds = array_keys($this->_objectManager->get('Magento\Store\Model\WebsiteFactory')->create()->getCollection()->toOptionHash());
        $actionModel = $this->_objectManager->create('Magento\Catalog\Model\Product\Action');
        $this->updateWebsites($productIds, $removeWebsiteIds, 'remove');
            
        foreach ($collection as $row){
            if(!$this->canShow($row->getVendorId())) {
                continue;
            }
            $productWebsiteIds=explode(',', $row->getWebsiteIds());
            if(!$previousVendorId || $previousVendorId!=$row->getVendorId()) {
                $vendorWebsiteIds=$this->_objectManager->get('Ced\CsMarketplace\Model\Vendor')->getWebsiteIds($row->getVendorId());
            }
            $previousVendorId=$row->getVendorId();
            $websiteIds=array_intersect($productWebsiteIds, $vendorWebsiteIds);
            if($websiteIds) {
                $this->updateWebsites(array($row->getProductId()), $websiteIds, 'add'); 
            }
        }
            
        $indexCollection = $this->_objectManager->create('Magento\Indexer\Model\Processor')->getCollection();
        foreach ($indexCollection as $index) {
            /* @var $index Magento\Indexer\Model\Processor */
            $index->reindexAll();
        }
        $this->cleanCache();
            
       $this->_objectManager->create('Magento\Framework\App\Config\Element')->saveConfig(\Ced\CsMarketplace\Model\Vendor::XML_PATH_VENDOR_WEBSITE_SHARE, 0);
        
    }
    /**
     * Clear cache related with product id
     *
     * @return $this
     */
    public function cleanCache()
    {
		$types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
		foreach ($types as $type) {
		    $this->_cacheTypeList->cleanType($type);
		}
		foreach ($this->_cacheFrontendPool as $cacheFrontend) {
		    $cacheFrontend->getBackend()->clean();
		}
    }
    
    /**
     *update Websites
     *
     *@param productIds, $websiteIds,$type
     */
     public function updateWebsites($productIds, $websiteIds, $type)
    {
        $this->_eventManager->dispatch(
                'catalog_product_website_update_before',
                [
                'website_ids'   => $websiteIds,
                'product_ids'   => $productIds,
                'action'        => $type                        
                ]
        );
        if ($type == 'add') {
            $this->_objectManager->get('Magento\Catalog\Model\Product\Website')->addProducts($websiteIds, $productIds);
        } else if ($type == 'remove') {
            $this->_objectManager->get('Magento\Catalog\Model\Product\Website')->removeProducts($websiteIds, $productIds);
        }
    
        $actionModel = $this->_objectManager->create('Magento\Catalog\Model\Product\Action');
        $actionModel->setData(
            array(
            'product_ids' => array_unique($productIds),
            'website_ids' => $websiteIds,
            'action_type' => $type
            )
        );
        
        $this->_eventManager->dispatch(
                'catalog_product_website_update',
                [
                        'website_ids'   => $websiteIds,
                        'product_ids'   => $productIds,
                        'action'        => $type
                ]
        );
    }
    
    
    
    /**
     * Get new vendor collection
     *
     * @return Ced_CsMarketplace_Model_Resource_Vendor_Collection
     */
    public function getNewVendors() 
    {        
        return $this->_objectManager->get('Ced\CsMarketplace\Model\Vendor\Collection')->addAttributeToFilter('status', array('eq'=>\Ced\CsMarketplac\_Model\Vendor::VENDOR_NEW_STATUS));
    }
    
    
    public function getFilterParams() 
    {
        return array (
        '_secure' => true,
        \Ced\CsMarketplace\Block\Adminhtml\Vendor\Entity\Grid::VAR_NAME_FILTER => base64_encode('status='.\Ced\CsMarketplace\Model\Vendor::VENDOR_NEW_STATUS),
               );
    }
    
    
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorLogEnabled()
    {
        return $this->getStoreConfig('ced_csmarketplace/vlogs/active', $this->getStore()->getId());
    }

    
    /**
     * Get current store
     *
     * @return Magento\Store\Model\Store
     */
    public function getStore()
    {
        
        $storeId = (int) $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getParam('store', 0);
        if($storeId) {
            return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore($storeId); 
        }
        else { 
            return $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore(null); 
        }
    }
     
    public function getRootId()
    {
        return $this->_storeManager->getStore()->getRootCategoryId();
    }
     
    /**
     * Log Process Data
     */
    public function logProcessedData($data, $tag=false) 
    {
     
        if(!$this->isVendorLogEnabled()) {
            return; 
        }
            
        $file = $this->getStoreConfig('ced_vlogs/general/process_file');
                
        $controller = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getControllerName();
        $action = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getActionName();
        $router = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getRouteName();
        $module = $this->_objectManager->get('Magento\Framework\App\RequestInterface')->getModuleName();
        
        $out = '';
        //if ($html) 
        @$out .= "<pre>";
        @$out .= "Controller: $controller\n";
        @$out .= "Action: $action\n";
        @$out .= "Router: $router\n";
        @$out .= "Module: $module\n";
        foreach(debug_backtrace() as $key=>$info)
        {
            @$out .= "#" . $key . " Called " . $info['function'] ." in " . $info['file'] . " on line " . $info['line']."\n"; 
            break;        
        }
        if($tag) {
            @$out .= "#Tag " . $tag."\n"; 
        } 
            
        //if ($html)
        @$out .= "</pre>";
        $this->_logger->critical("\n Source: \n" . print_r($out, true), null,$file,true);
        $this->_logger->critical("\n Processed Data: \n" . print_r($data, true), null,$file,true);
    }
     
     
    /**
     * Log Exception
     */
    public function logException(\Exception $e) 
    {
        if(!$this->isVendorLogEnabled()) {
            return; 
        }
            
        $file = $this->getStoreConfig('ced_vlogs/general/exception_file');
       $this->_logger->critical("\n" . $e->__toString(), null,$file,true);
        
    }
     
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isVendorDebugEnabled()
    {
        $isDebugEnable = (int)$this->getStoreConfig('ced_csmarketplace/vlogs/debug_active');
        $clientIp = $this->_getRequest()->getClientIp();
        $allow = false;

        if($isDebugEnable ) {
            $allow = true;

            // Code copy-pasted from core/helper, isDevAllowed method 
            // I cannot use that method because the client ip is not always correct (e.g varnish)
            $allowedIps = $this->getStoreConfig('dev/restrict/allow_ips');
            if ($isDebugEnable && !empty($allowedIps) && !empty($clientIp)) {
                $allowedIps = preg_split('#\s*,\s*#', $allowedIps, null, PREG_SPLIT_NO_EMPTY);
                if (array_search($clientIp, $allowedIps) === false
                    && array_search($this->_request->getHttpHost(), $allowedIps) === false
                ) {
                    $allow = false;
                }
            }
        }
        return $allow;   
    }
    
    /**
     * Check Vendor Log is enabled
     *
     * @return boolean
     */
    public function isShopEnabled($vendor)
    {
        $model = $this->_objectManager->get('Ced\CsMarketplace\Model\Vshop')->loadByField(array('vendor_id'), array($vendor->getId()));

        if($model && $model->getId()) {
            if($model->getShopDisable() == \Ced\CsMarketplace\Model\Vshop::DISABLED) {
                return false; 
            }
        }
        return true;
    }
    
    /**
     * Function for setting Config value of current store
     *
     * @param string $path,
     * @param string $value,
     */
    public function setStoreConfig($path, $value, $storeId=null)
    {
        $store=$this->_storeManager->getStore($storeId);
        $data = [
                    'path' => $path,
                    'scope' =>  'stores',
                    'scope_id' => $storeId,
                    'scope_code' => $store->getCode(),
                    'value' => $value,
                ];
        $this->_configValueManager->addData($data);
        $this->_transaction->addObject($this->_configValueManager);
        $this->_transaction->save();
    }
    
    /**
     * Function for getting Config value of current store
     *
     * @param string $path,
     */
    public function getStoreConfig($path,$storeId=null)
    {
    
        $store=$this->_storeManager->getStore($storeId);
        return $this->_scopeConfigManager->getValue($path, 'store', $store->getCode());
    }
    
    public function getTableKey($key)
    {
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $tablePrefix = (string) $this->_objectManager->get('Magento\Framework\App\DeploymentConfig')
            ->get(\Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);
        $exists = $resource->getConnection('core_write')->showTableStatus($tablePrefix.'permission_variable');
        if($exists) {
            return $key;
        }else{
            return "{$key}";
        }
    }
    
    public function getCsMarketplaceLink()
    {
    	if($this->scopeConfig->getValue('ced_csmarketplace/general/activation'))
    	{
    	   return $this->scopeConfig->getValue('ced_vshops/general/vshoppage_top_title');
    	}
   	
    }
    
    public function getIamaVendorLink()
    {
    	if($this->scopeConfig->getValue('ced_csmarketplace/general/activation'))
    	{
    		return $this->scopeConfig->getValue('ced_vshops/general/vshoppage_title');
    	}
    
    }
}
