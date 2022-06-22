<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Observer;

class SaveConfig implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\JsonConfig
     */
    protected $_jsonConfigHelper = null;

    /**
     * Class constructor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_jsonConfigHelper = $jsonConfigHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $storeId = $observer->getStore();
        $storeCodes = [];

        if ($storeId) {
            // Store view
            $store = $this->_storeManager->getStore($storeId);
            $storeCodes = [$store->getCode()];
        } else {
            // Default Config
            $stores = $this->_storeManager->getStores();
            foreach ($stores as $store) {
                $storeCodes[] = $store->getCode();
            }
        }

        foreach ($storeCodes as $code) {
            $this->_jsonConfigHelper->saveConfig($code);
        }
    }
}