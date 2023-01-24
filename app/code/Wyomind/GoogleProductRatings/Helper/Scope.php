<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Helper;

class Scope
{
    /**
     * @var string
     */
    protected $_directory;
    /**
     * @var string
     */
    protected $_scope;
    /**
     * @var string
     */
    protected $_scopeId;
    /**
     * @var array
     */
    protected $_storeIds;
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * Store usefull scope infirmation
     *
     * @param array $scopeIds
     */
    public function initScope($scopeIds)
    {
        $websiteId = array_key_exists('website', $scopeIds) ? $scopeIds['website'] : null;
        $storeId = array_key_exists('store', $scopeIds) ? $scopeIds['store'] : null;
        if ($storeId) {
            // Store view
            $store = $this->_storeManager->getStore($storeId);
            $storeCode = $store->getCode();
            $websiteCode = $store->getWebsite()->getCode();
            $groupCode = $store->getGroup()->getCode();
            $this->_directory = DIRECTORY_SEPARATOR . $websiteCode . DIRECTORY_SEPARATOR . $groupCode . DIRECTORY_SEPARATOR . $storeCode;
            $this->_scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
            $this->_scopeId = $storeId;
            $this->_storeIds = [$storeId];
            $this->_storeManager->setCurrentStore($storeId);
        } elseif ($websiteId) {
            // Website
            $website = $this->_storeManager->getWebsite($websiteId);
            $websiteCode = $website->getCode();
            $this->_directory = DIRECTORY_SEPARATOR . $websiteCode;
            $this->_scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
            $this->_scopeId = $websiteId;
            $this->_storeIds = array_values($website->getStoreIds());
            $this->_storeManager->setCurrentStore($this->_storeIds[0]);
        } else {
            // Default Config
            $this->_directory = '';
            $this->_scope = 'default';
            $this->_scopeId = 0;
            $this->_storeIds = [];
            $stores = $this->_storeManager->getStores();
            foreach ($stores as $store) {
                $this->_storeIds[] = $store->getId();
            }
            $this->_storeManager->setCurrentStore(0);
        }
    }
    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->_directory;
    }
    /**
     * @return string
     */
    public function getScope()
    {
        return $this->_scope;
    }
    /**
     * @return string
     */
    public function getScopeId()
    {
        return $this->_scopeId;
    }
    /**
     * @return array
     */
    public function getStoreIds()
    {
        return $this->_storeIds;
    }
}