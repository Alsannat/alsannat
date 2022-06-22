<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\System\Message;

class Notification implements \Magento\Framework\Notification\MessageInterface
{
    /**
     * Message identity
     */
    const MESSAGE_IDENTITY = 'wyomind_elasticsearchcore_notification';

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $_cacheManager = null;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface\Proxy
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory = null;

    /**
     * @var boolean
     */
    public $_warnings = 0;

    /**
     * @var string
     */
    public $_content = '';

    /**
     * Notifications constructor
     * @param \Magento\Framework\App\CacheInterface $cacheManager
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Magento\Framework\App\CacheInterface $cacheManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
    )
    {
        $this->_cacheManager = $cacheManager;
        $this->_urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->_configHelper = $configHelper;
        $this->_indexerHelperFactory = $indexerHelperFactory;
    }

    public function checkNotifications()
    {

        $html = '';

        // PHP elasticsearch library not installed
        if (false === class_exists('\GuzzleHttp\Ring\Core')) {
            $this->_warnings++;

            $html .= '<div><b> Wyomind ElasticsearchCore</b><br/>'
                . __('The PHP library RingPHP is not installed. <br/> Please run composer require guzzlehttp/ringphp.')
                . '</div><br/>';
        }

        // Server failed notifications
        if ($this->_configHelper->isServerStatusBackendNotificationEnabled()) {
            $content = '';
            foreach ($this->_storeManager->getStores() as $store) {
                $storeId = $store->getStoreId();

                if (0 ==  $this->_configHelper->getServerStatus($storeId)) {
                    $this->_warnings++;

                    $storeCode = $store->getCode();
                    $storeName = $store->getName();
                    $servers = $this->_configHelper->getStoreConfig(\Wyomind\ElasticsearchCore\Helper\Config::XML_PATH_CORE_SERVERS, $storeId);
                    $serverVersion = $this->_configHelper->getServerVersion();

                    $configContent = $this->_configHelper->getServerStatusBackendNotificationContent();
                    $content .= str_replace(
                            ['{{store_id}}', '{{code}}', '{{name}}', '{{server_version}}', '{{servers}}'],
                            [$storeId, $storeCode, $storeName, $serverVersion, $servers],
                            $configContent
                        ) . '<br/>';
                }
            }

            if ('' !== $content) {
                $subject = $this->_configHelper->getServerStatusBackendNotificationSubject();
                $html .= '<div><b>' . $subject . '</b><br/>' . $content
                    . __('Please check your configuration and the log file var/log/Wyomind_ElasticsearchCore_Server_Status.log.')
                    . '</div><br/>';
            }
        }

        $indexers = $this->_indexerHelperFactory->create()->getAllIndexers();

        foreach ($indexers as $indexer) {
            if (1 != $indexer->getReindexed() && $this->_configHelper->getStoreConfig("wyomind_elasticsearchcore/types/".$indexer->getType()."/enable")) {
                $this->_warnings++;

                $html .= '<div><b> Wyomind ElasticsearchCore</b><br/>'
                    . $indexer->getName() . __(' index needs to be reindexed. <br/> Please run bin/magento wyomind:elasticsearchcore:indexer:reindex ' . $indexer->getType())
                    . '<br/>' . __('or go to <a href="' . $this->_urlBuilder->getUrl(\Wyomind\ElasticsearchCore\Helper\Url::MANAGE_INDEXES) . '">System > Wyomind > Elasticsearch Core > Manage Indexes</a>')
                    . '</div><br/>';
            }
        }

        $this->_content = $html;
    }

    /**
     * Retrieve unique system message identity
     * @return string
     */
    public function getIdentity()
    {
        return self::MESSAGE_IDENTITY;
    }

    /**
     * Check whether the system message should be shown
     * @return bool
     */
    public function isDisplayed()
    {
        $this->checkNotifications();
        return $this->_warnings > 0;
    }

    /**
     * Retrieve system message text
     * @return string
     */
    public function getText()
    {
        return $this->_content;
    }

    /**
     * Retrieve system message severity
     * Possible default system message types:
     *  - MessageInterface::SEVERITY_CRITICAL
     *  - MessageInterface::SEVERITY_MAJOR
     *  - MessageInterface::SEVERITY_MINOR
     *  - MessageInterface::SEVERITY_NOTICE
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }
}