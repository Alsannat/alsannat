<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Indexer;

class Context
{
    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor = null;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $_messageManager = null;

    /**
     * @var \Magento\Framework\Module\ModuleList
     */
    protected $_moduleList = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate = null;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Symfony\Component\Console\Output\ConsoleOutputFactory
     */
    protected $_consoleOutputFactory = null;

    /**
     * @var \Wyomind\Core\Helper\Data
     */
    private $_coreHelper;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Attribute
     */
    protected $_attributeHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    private $_configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\JsonConfig
     */
    protected $_jsonConfigHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Log
     */
    protected $_logHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Session
     */
    protected $_sessionHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Adapter
     */
    protected $_adapter = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Index
     */
    protected $_indexModel = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\ToReindexFactory
     */
    protected $_toReindexModelFactory = null;

    /**
     * @var Wyomind\ElasticsearchCore\Model\ResourceModel\Config
     */
    protected $_configResourceModel = null;

    /**
     * Context constructor.
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Module\ModuleList $moduleList
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Symfony\Component\Console\Output\ConsoleOutputFactory $consoleOutputFactory
     * @param \Wyomind\Core\Helper\Data $coreHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Attribute $attributeHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     * @param \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Log $logHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper
     * @param \Wyomind\ElasticsearchCore\Model\Adapter $adapter
     * @param \Wyomind\ElasticsearchCore\Model\Index $indexModel
     * @param \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory
     * @param \Wyomind\ElasticsearchCore\Model\ResourceModel\Config $configResourceModel
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Module\ModuleList $moduleList,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Symfony\Component\Console\Output\ConsoleOutputFactory $consoleOutputFactory,
        \Wyomind\Core\Helper\Data $coreHelper,
        \Wyomind\ElasticsearchCore\Helper\Attribute $attributeHelper,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory,
        \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper,
        \Wyomind\ElasticsearchCore\Helper\Log $logHelper,
        \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper,
        \Wyomind\ElasticsearchCore\Model\Adapter $adapter,
        \Wyomind\ElasticsearchCore\Model\Index $indexModel,
        \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory,
        \Wyomind\ElasticsearchCore\Model\ResourceModel\Config $configResourceModel
    )
    {
        $this->_encryptor = $encryptor;
        $this->_eventManager = $eventManager;
        $this->_messageManager = $messageManager;
        $this->_moduleList = $moduleList;
        $this->_objectManager = $objectManager;
        $this->_coreDate = $coreDate;
        $this->_urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->_consoleOutputFactory = $consoleOutputFactory;
        $this->_coreHelper = $coreHelper;
        $this->_attributeHelper = $attributeHelper;
        $this->_configHelper = $configHelper;
        $this->_indexerHelperFactory = $indexerHelperFactory;
        $this->_jsonConfigHelper = $jsonConfigHelper;
        $this->_logHelper = $logHelper;
        $this->_sessionHelper = $sessionHelper;
        $this->_adapter = $adapter;
        $this->_indexModel = $indexModel;
        $this->_toReindexModelFactory = $toReindexModelFactory;
        $this->_configResourceModel = $configResourceModel;
    }

    /**
     * @return \Magento\InventoryIndexer\Model\StockIndexTableNameResolver\Proxy
     */
    public function getStockIndexTableNameResolver()
    {
        if ($this->_coreHelper->moduleIsEnabled("Magento_InventoryIndexer") && $this->_coreHelper->moduleIsEnabled("Magento_InventorySales")) {
            if (class_exists("\Magento\InventoryIndexer\Model\StockIndexTableNameResolver")) {
                return $this->_objectManager->get("\Magento\InventoryIndexer\Model\StockIndexTableNameResolver");
            }
        }
        return null;
    }

    /**
     * @return \Magento\InventorySalesApi\Api\StockResolverInterface\Proxy
     */
    public function getStockResolver()
    {
        if ($this->_coreHelper->moduleIsEnabled("Magento_InventoryIndexer") && $this->_coreHelper->moduleIsEnabled("Magento_InventorySales")) {
            if (class_exists("\Magento\InventorySales\Model\StockResolver")) {
                return $this->_objectManager->get("\Magento\InventorySales\Model\StockResolver");
            }
        }
        return null;
    }

    public function getSelectBuilder()
    {
        if ($this->_coreHelper->moduleIsEnabled("Magento_InventoryIndexer") && $this->_coreHelper->moduleIsEnabled("Magento_InventorySales")) {
            if (class_exists("\Magento\InventoryIndexer\Indexer\SelectBuilder")) {
                return $this->_objectManager->get("\Magento\InventoryIndexer\Indexer\SelectBuilder");
            }
        }
        return null;
    }


    /**
     * @return \Magento\Framework\Encryption\EncryptorInterface|null
     */
    public function getEncryptor()
    {
        return $this->_encryptor;
    }

    /**
     * @return \Magento\Framework\Event\ManagerInterface
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }

    /**
     * @return \Magento\Framework\Message\ManagerInterface
     */
    function getMessageManager()
    {
        return $this->_messageManager;
    }

    /**
     * @return \Magento\Framework\Module\ModuleList
     */
    public function getModuleList()
    {
        return $this->_moduleList;
    }

    /**
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getCoreDate()
    {
        return $this->_coreDate;
    }

    /**
     * @return \Magento\Framework\UrlInterface|null
     */
    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    /**
     * @return \Symfony\Component\Console\Output\ConsoleOutputFactory
     */
    public function getConsoleOutputFactory()
    {
        return $this->_consoleOutputFactory;
    }

    /**
     * @return \Wyomind\Core\Helper\Data
     */
    public function getCoreHelper()
    {
        return $this->_coreHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\Attribute
     */
    public function getAttributeHelper()
    {
        return $this->_attributeHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\Config
     */

    public function getConfigHelper()
    {
        return $this->_configHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    public function getIndexerHelperFactory()
    {
        return $this->_indexerHelperFactory;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\JsonConfig
     */
    public function getJsonConfigHelper()
    {
        return $this->_jsonConfigHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\Log
     */
    public function getLogHelper()
    {
        return $this->_logHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Helper\Session
     */
    public function getSessionHelper()
    {
        return $this->_sessionHelper;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Model\Adapter
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Model\Index
     */
    public function getIndexModel()
    {
        return $this->_indexModel;
    }

    /**
     * @return \Wyomind\ElasticsearchCore\Model\ToReindexFactory
     */
    public function getToReindexModelFactory()
    {
        return $this->_toReindexModelFactory;
    }

    /**
     * @return Wyomind\ElasticsearchCore\Model\ResourceModel\Config|null
     */
    public function getResourceModelConfig()
    {
        return $this->_configResourceModel;
    }
}