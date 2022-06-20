<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Cron;

/**
 * Class CheckElasticsearchServerStatus
 */
class CheckElasticsearchServerStatus
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface\Proxy
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Server
     */
    protected $_serverHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * Class constructor
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Server $_serverHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Config $_configHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Server $_serverHelper,
        \Wyomind\ElasticsearchCore\Helper\Config $_configHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_serverHelper = $_serverHelper;
        $this->_configHelper = $_configHelper;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        // Global scope
        $cleanCache = $this->_serverHelper->updateServerCron();

        // Other scopes
        foreach ($this->_storeManager->getStores() as $store) {
            $storeId = $store->getStoreId();
            $storeCode = $store->getCode();
            $cleanCache |= $this->_serverHelper->updateServerCron($storeId, $storeCode, true);
        }

        if ($cleanCache) {
            $this->_configHelper->cleanCache();
        }
    }
}