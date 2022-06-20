<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Class Config
 * @package Wyomind\ElasticsearchCore\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper implements ConfigInterface
{
    const XML_PATH_CORE_ELASTICSEARCH_SERVER_STATUS = 'wyomind_elasticsearchcore/configuration/elasticsearch_server_status';
    const XML_PATH_CORE_SERVER_VERSION = 'wyomind_elasticsearchcore/configuration/server_version';
    const XML_PATH_CORE_COMPATIBILITY = 'wyomind_elasticsearchcore/configuration/compatibility';
    const XML_PATH_CORE_SERVERS = 'wyomind_elasticsearchcore/configuration/servers';
    const XML_PATH_CORE_VERIFY_HOST = 'wyomind_elasticsearchcore/configuration/verify_host';
    const XML_PATH_CORE_TIMEOUT = 'wyomind_elasticsearchcore/configuration/timeout';
    const XML_PATH_CORE_INDEX_PREFIX = 'wyomind_elasticsearchcore/configuration/index_prefix';
    const XML_PATH_CORE_INDEX_SETTINGS = 'wyomind_elasticsearchcore/configuration/index_settings';
    const XML_PATH_CORE_SAFE_REINDEX = 'wyomind_elasticsearchcore/configuration/safe_reindex';
    const XML_PATH_CORE_QUERY_OPERATOR = 'wyomind_elasticsearchcore/configuration/query_operator';
    const XML_PATH_CORE_ENABLE_FUZZY_QUERY = 'wyomind_elasticsearchcore/configuration/enable_fuzzy_query';
    const XML_PATH_CORE_FUZZY_QUERY_MODE = 'wyomind_elasticsearchcore/configuration/fuzzy_query_mode';
    const XML_PATH_CORE_ENABLE_PRODUCT_WEIGHT = 'wyomind_elasticsearchcore/configuration/enable_product_weight';
    const XML_PATH_CORE_ENABLE_PUB_FOLDER = 'wyomind_elasticsearchcore/configuration/enable_pub_folder';

    const XML_PATH_CORE_DEBUG_ENABLE_REQUEST_LOG = 'wyomind_elasticsearchcore/debug/enable_frontend_request_log';
    const XML_PATH_CORE_DEBUG_ENABLE_REINDEX_LOG = 'wyomind_elasticsearchcore/debug/enable_reindex_log';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_LOG = 'wyomind_elasticsearchcore/debug/enable_elasticsearch_sever_status_log';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION = 'wyomind_elasticsearchcore/debug/backend_notification_on_elasticsearch_sever_fail';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION_SUBJECT = 'wyomind_elasticsearchcore/debug/backend_notification_on_elasticsearch_sever_fail_settings/subject';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION_CONTENT = 'wyomind_elasticsearchcore/debug/backend_notification_on_elasticsearch_sever_fail_settings/content';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_MAIL_NOTIFICATION = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_SENDER_MAIL = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail_settings/sender_email';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_SENDER_NAME = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail_settings/sender_name';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_EMAILS = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail_settings/emails';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_SUBJECT = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail_settings/subject';
    const XML_PATH_CORE_DEBUG_SERVER_STATUS_CONTENT = 'wyomind_elasticsearchcore/debug/mail_notification_on_elasticsearch_sever_fail_settings/content';
    const MAIL_NOTIFICATION_TEMPLATE = "wyomind_elasticsearchcore_server_failed";

    const XML_PATH_ESA_ENABLE = 'wyomind_elasticsearchautocomplete/settings/enable';
    const XML_PATH_EMA_ENABLE = 'wyomind_elasticsearchmultifacetedautocomplete/settings/enable';
    const XML_PATH_EELN_ENABLE = 'wyomind_elasticsearchlayerednavigation/settings/enable';
    const XML_PATH_CAN_USE_ESA = 'wyomind_elasticsearchcore/configuration/can_use_elasticsearchautocomplete';
    const XML_PATH_CAN_USE_EMA = 'wyomind_elasticsearchcore/configuration/can_use_elasticsearchmultifacetedautocomplete';
    const XML_PATH_CAN_USE_EELN = 'wyomind_elasticsearchcore/configuration/can_use_elasticsearchlayerednavigation';
    const XML_PATH_CANT_USE_ESA = 'wyomind_elasticsearchcore/configuration/cant_use_elasticsearchautocomplete';
    const XML_PATH_CANT_USE_EMA = 'wyomind_elasticsearchcore/configuration/cant_use_elasticsearchmultifacetedautocomplete';
    const XML_PATH_CANT_USE_EELN = 'wyomind_elasticsearchcore/configuration/cant_use_elasticsearchlayerednavigation';

    const XML_PATH_DESIGN_PRIMARY_COLOR = 'wyomind_elasticsearchcore/design/primary_color';
    const XML_PATH_DESIGN_SECONDARY_COLOR = 'wyomind_elasticsearchcore/design/secondary_color';
    const XML_PATH_DESIGN_BACKGROUND_PRIMARY_COLOR = 'wyomind_elasticsearchcore/design/background_primary_color';
    const XML_PATH_DESIGN_BACKGROUND_SECONDARY_COLOR = 'wyomind_elasticsearchcore/design/background_secondary_color';
    const XML_PATH_DESIGN_TRANSITION_ENABLE = 'wyomind_elasticsearchcore/design/transition_enable';
    const XML_PATH_DESIGN_OVERLAY_ENABLE = 'wyomind_elasticsearchcore/design/overlay_enable';
    const XML_PATH_DESIGN_TRANSITION_DURATION = 'wyomind_elasticsearchcore/design/transition_duration';
    const XML_PATH_DESIGN_BLUR_ENABLE = 'wyomind_elasticsearchcore/design/blur_enable';

    const CATEGORIES_ID = 'id';
    const CATEGORIES_URL = 'url';
    const CATEGORIES_PATH = 'path';
    const CMS_ID = 'id';
    const PRODUCT_CATEGORIES_PARENT_ID = 'categories_parent_ids';
    const PRODUCT_CATEGORIES_ID = 'categories_ids';
    const PRODUCT_CATEGORIES = 'categories';
    const PRODUCT_SHORTEST_URL = 'shortest_url';
    const PRODUCT_LONGEST_URL = 'longest_url';
    const PRODUCT_PARENT_IDS = 'parent_ids';
    const PRODUCT_PRICES = 'prices';
    const PRODUCT_URL = 'url';
    const NAME_SUGGESTER = 'name_suggester';
    const SKU_SUGGESTER = 'sku_suggester';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface|null
     */
    public $encryptor = null;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    public $scopeConfig = null;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    public $cacheManager = null;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    public $config = null;

    /**
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Model\Context $contextBis
     * @param \Magento\Config\Model\ResourceModel\Config $config
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Model\Context $contextBis,
        \Magento\Config\Model\ResourceModel\Config $config
    )
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->config = $config;
        $this->scopeConfig = $context->getScopeConfig();
        $this->cacheManager = $contextBis->getCacheManager();
    }


    /**
     * Get value from store config
     * @param string $key
     * @param null|string $scopeId
     * @return mixed
     */
    public function getStoreConfig($key, $scopeId = null)
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        if (!$scopeId) {
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        return $this->scopeConfig->getValue($key, $scope, $scopeId);
    }

    /**
     * Set Store Config
     * @param string $key
     * @param string $value
     * @param int $scopeId
     */
    public function setStoreConfig($key, $value, $scopeId = 0, $cleanCache = false)
    {
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
        if (!$scopeId) {
            $scope = \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT;
        }

        $this->config->saveConfig($key, $value, $scope, $scopeId);
        if ($cleanCache) {
            $this->cleanCache();
        }
    }

    /**
     *
     */
    public function cleanCache()
    {
        $this->cacheManager->clean(['config']);
    }

    /**
     * get uncrypted default config value
     * @param string $key
     * @return string
     */
    public function getStoreConfigUncrypted($key)
    {
        return $this->encryptor->decrypt($this->scopeConfig->getValue($key, \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT));
    }

    /**
     * Set default config crypted value
     * @param string $key
     * @param string $value
     */
    public function setStoreConfigCrypted(
        $key,
        $value
    )
    {
        $this->config->saveConfig($key, $this->encryptor->encrypt($value), \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
        $this->cacheManager->clean(['config']);
    }

    /**
     * @param array $data
     * @param int $scopeId
     * @param bool $cleanCache
     * @return bool
     */
    public function updateCanUseModulesInLayout($data = [], $scopeId = 0, $cleanCache = true)
    {

        $canUseAutocomplete = $this->getServerStatus();
        if (isset($data['autocomplete'])) {
            $canUseAutocomplete &= $data['autocomplete'] != 0;
        } else {
            $canUseAutocomplete &= $this->getStoreConfig(self::XML_PATH_ESA_ENABLE, $scopeId) != 0;
        }

        $canUseMultifacetedAutocomplete = $this->getServerStatus();
        if (isset($data['multifacetedautocomplete'])) {
            $canUseMultifacetedAutocomplete &= $data['multifacetedautocomplete'] != 0;
        } else {
            $canUseMultifacetedAutocomplete &= $this->getStoreConfig(self::XML_PATH_EMA_ENABLE, $scopeId) != 0;
        }

        $canUseLayeredNavigation = $this->getServerStatus();
        if (isset($data['layerednavigation'])) {
            $canUseLayeredNavigation &= $data['layerednavigation'] != 0;
        } else {
            $canUseLayeredNavigation &= $this->getStoreConfig(self::XML_PATH_EELN_ENABLE, $scopeId) != 0;
        }

        if ($canUseMultifacetedAutocomplete) {
            $canUseAutocomplete = false;
        }

        $newCleanCache = false;

        $oldCanUseAutocomplete = $this->getStoreConfig(self::XML_PATH_CAN_USE_ESA, $scopeId);

        if ($oldCanUseAutocomplete !== $canUseAutocomplete) {
            $this->setStoreConfig(self::XML_PATH_CAN_USE_ESA, $canUseAutocomplete ? 1 : 0, $scopeId, $cleanCache);
            $this->setStoreConfig(self::XML_PATH_CANT_USE_ESA, $canUseAutocomplete ? 0 : 1, $scopeId, $cleanCache);
            $newCleanCache = true;
        }

        $oldCanUseMultifacetedAutocomplete = $this->getStoreConfig(self::XML_PATH_CAN_USE_EMA, $scopeId);
        if ($oldCanUseMultifacetedAutocomplete !== $canUseMultifacetedAutocomplete) {
            $this->setStoreConfig(self::XML_PATH_CAN_USE_EMA, $canUseMultifacetedAutocomplete ? 1 : 0, $scopeId, $cleanCache);
            $this->setStoreConfig(self::XML_PATH_CANT_USE_EMA, $canUseMultifacetedAutocomplete ? 0 : 1, $scopeId, $cleanCache);
            $newCleanCache = true;
        }
        $oldCanUseLayeredNavigation = $this->getStoreConfig(self::XML_PATH_CAN_USE_EMA, $scopeId);
        if ($oldCanUseLayeredNavigation !== $canUseLayeredNavigation) {
            $this->setStoreConfig(self::XML_PATH_CAN_USE_EELN, $canUseLayeredNavigation ? 1 : 0, $scopeId, $cleanCache);
            $this->setStoreConfig(self::XML_PATH_CANT_USE_EELN, $canUseLayeredNavigation ? 0 : 1, $scopeId, $cleanCache);
            $newCleanCache = true;
        }
        return $newCleanCache;
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function canUseLayeredNavigation($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CAN_USE_EELN, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function canUseMultifacetedAutocomplete($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CAN_USE_EMA, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function canUseElasticsearchAutocomplete($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CAN_USE_ESA, $scopeId);
    }

    /**
     * @param string $path
     * @param mixed $scopeId
     * @return bool
     */
    public function getFlag($path, $scopeId = null)
    {
        return $this->scopeConfig->isSetFlag($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $scopeId);
    }

    // Elasticsearch Server status

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getServerStatus($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_ELASTICSEARCH_SERVER_STATUS, $scopeId);
    }

    /**
     * @param $status
     * @param int $scopeId
     * @param bool $cleanCache
     */
    public function setServerStatus($status, $scopeId = 0, $cleanCache = true)
    {
        $this->setStoreConfig(self::XML_PATH_CORE_ELASTICSEARCH_SERVER_STATUS, $status, $scopeId, $cleanCache);
    }

    // Elasticsearch Server version

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getServerVersion($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_SERVER_VERSION, $scopeId);
    }

    /**
     * @param $version
     * @param int $scopeId
     * @param bool $cleanCache
     */
    public function setServerVersion($version, $scopeId = 0, $cleanCache = true)
    {
        $this->setStoreConfig(self::XML_PATH_CORE_SERVER_VERSION, $version, $scopeId, $cleanCache);

    }

    // Elasticsearch Server compatibility

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getCompatibility($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_COMPATIBILITY, $scopeId);
    }

    /**
     * @param $compatibility
     * @param int $scopeId
     * @param bool $cleanCache
     */
    public function setCompatibility($compatibility, $scopeId = 0, $cleanCache = true)
    {
            $this->setStoreConfig(self::XML_PATH_CORE_COMPATIBILITY, $compatibility, $scopeId, $cleanCache);

    }

    // Elasticsearch Servers (host:port)

    /**
     * @param null $scopeId
     * @return array
     */
    public function getServers($scopeId = null)
    {
        return explode(',', $this->getStoreConfig(self::XML_PATH_CORE_SERVERS, $scopeId));
    }

    // Is a verified host

    /**
     * @param null $scopeId
     * @return bool
     */
    public function isVerifyHost($scopeId = null)
    {
        return (bool)$this->getStoreConfig(self::XML_PATH_CORE_VERIFY_HOST, $scopeId);
    }

    // Connection timeout in seconds

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getConnectTimeout($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_TIMEOUT, $scopeId);
    }

    // Index prefix used to avoid potential collisions

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getIndexPrefix($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_INDEX_PREFIX, $scopeId);
    }

    // Index settings

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getIndexSettings($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_INDEX_SETTINGS, $scopeId);
    }

    // Safe reindex

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function isSafeReindex($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_SAFE_REINDEX, $scopeId);
    }

    // Get query operator (AND or OR)

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getQueryOperator($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_QUERY_OPERATOR, $scopeId);
    }

    // Is the approximate search enabled

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function isFuzzyQueryEnabled($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_ENABLE_FUZZY_QUERY, $scopeId);
    }

    // Fuzzy query mode (AUTO, 0, 1, 2)

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getFuzzyQueryMode($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_FUZZY_QUERY_MODE, $scopeId);
    }

    // Product weight

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function isProductWeightEnabled($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_ENABLE_PRODUCT_WEIGHT, $scopeId);
    }

    // 'pub' folder in images url

    /**
     * @param null $scopeId
     * @return bool
     */
    public function getEnablePubFolder($scopeId = null)
    {
        return $this->getFlag(self::XML_PATH_CORE_ENABLE_PUB_FOLDER, $scopeId);
    }

    // Debug parameters

    /**
     * @return mixed
     */
    public function isFrontendLogEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_ENABLE_REQUEST_LOG);
    }

    /**
     * @return mixed
     */
    public function isReindexLogEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_ENABLE_REINDEX_LOG);
    }

    /**
     * @return mixed
     */
    public function isServerStatusLogEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_LOG);
    }

    /**
     * @return mixed
     */
    public function isServerStatusBackendNotificationEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION);
    }

    /**
     * @return mixed
     */
    public function getServerStatusBackendNotificationSubject()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION_SUBJECT);
    }

    /**
     * @return mixed
     */
    public function getServerStatusBackendNotificationContent()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_BACKEND_NOTIFICATION_CONTENT);
    }

    /**
     * @return mixed
     */
    public function isServerStatusMailNotificationEnabled()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_MAIL_NOTIFICATION);
    }

    /**
     * @return mixed
     */
    public function getServerStatusMailNotificationSenderMail()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_SENDER_MAIL);
    }

    /**
     * @return mixed
     */
    public function getServerStatusMailNotificationSenderName()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_SENDER_NAME);
    }

    /**
     * @return mixed
     */
    public function getServerStatusMailNotificationEmails()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_EMAILS);
    }

    /**
     * @return mixed
     */
    public function getServerStatusMailNotificationSubject()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_SUBJECT);
    }

    /**
     * @return mixed
     */
    public function getServerStatusMailNotificationContent()
    {
        return $this->getStoreConfig(self::XML_PATH_CORE_DEBUG_SERVER_STATUS_CONTENT);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignPrimaryColor($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_PRIMARY_COLOR, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignSecondaryColor($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_SECONDARY_COLOR, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignBackgroundPrimaryColor($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_BACKGROUND_PRIMARY_COLOR, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignBackgroundSecondaryColor($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_BACKGROUND_SECONDARY_COLOR, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignOverlayEnable($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_OVERLAY_ENABLE, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignTransitionEnable($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_TRANSITION_ENABLE, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignTransitionDuration($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_TRANSITION_DURATION, $scopeId);
    }

    /**
     * @param null $scopeId
     * @return mixed
     */
    public function getDesignBlurEnable($scopeId = null)
    {
        return $this->getStoreConfig(self::XML_PATH_DESIGN_BLUR_ENABLE, $scopeId);
    }

    /**
     * @param mixed $scopeId
     * @return string
     */
    public function getTheme($scopeId = null)
    {
        return $this->getStoreConfig(\Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID, $scopeId);
    }

    /**
     * @param mixed $scopeId
     * @return string
     */
    public function getLanguage($scopeId = null)
    {
        return \Locale::getDisplayLanguage($this->getLocaleCode($scopeId), 'en_US');
    }

    /**
     * @param mixed $scopeId
     * @return mixed
     */
    public function getLocaleCode($scopeId = null)
    {
        return $this->getStoreConfig(
            \Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_GENERAL_LOCALE_CODE,
            $scopeId
        );
    }

    /**
     * Returns searchable attribute codes available for given entity
     *
     * @param string $entity
     * @param mixed $scopeId
     * @return array
     */
    public function getEntitySearchableAttributes($entity, $scopeId = null)
    {
        if ($this->getStoreConfig('wyomind_elasticsearchcore/types/' . $entity . '/attributes', $scopeId) == null) {
            $scopeId = null;
        }
        return json_decode($this->getStoreConfig('wyomind_elasticsearchcore/types/' . $entity . '/attributes', $scopeId), true);
    }

    /**
     * @param string $entity
     * @param mixed $scopeId
     * @return string
     */
    public function isIndexationEnabled($entity, $scopeId = null)
    {
        if ($entity == "configurable_options") {
            return true;
        }
        return $this->getFlag('wyomind_elasticsearchcore/types/' . $entity . '/enable', $scopeId);
    }

    /**
     * @param mixed $scopeId
     * @return bool
     */
    public function isIndexOutOfStockProducts($scopeId = null)
    {
        return $this->getFlag(\Magento\CatalogInventory\Model\Configuration::XML_PATH_SHOW_OUT_OF_STOCK, $scopeId);
    }

    /**
     * @param mixed $scopeId
     * @return bool
     */
    public function isManageStock($scopeId = null)
    {
        return $this->getFlag(\Magento\CatalogInventory\Model\Configuration::XML_PATH_MANAGE_STOCK, $scopeId);
    }
}