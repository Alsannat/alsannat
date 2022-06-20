<?php
namespace Custom\City\Block;
class City extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Custom\City\Helper\Data $helper
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Custom\City\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function isEnabled(){
        return $this->helper->getConfig('city/general/enabled');
    }

    /**
     * @return mixed
     */
    public function isStateAvailable(){
        return $this->helper->getConfig('city/general/is_state_available');
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getConfigValue($path){
        return $this->helper->getConfig($path);
    }
}