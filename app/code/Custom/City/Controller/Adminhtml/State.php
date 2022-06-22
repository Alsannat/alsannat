<?php

namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\StateFactory;

abstract class State extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * city model factory
     *
     * @var \Custom\City\Model\StateFactory
     */
    protected $_stateFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param StateFactory $stateFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\File\Csv $csv
     * @param \Custom\City\Model\Resource\State\Collection $stateCollection
     * @param \Custom\City\Model\Resource\Statelocale\Collection $stateLocaleCollection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Custom\City\Model\Statelocale $stateLocale
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        StateFactory $stateFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csv,
        \Custom\City\Model\Resource\State\Collection $stateCollection,
        \Custom\City\Model\Resource\Statelocale\Collection $stateLocaleCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Custom\City\Model\Statelocale $stateLocale,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_stateFactory = $stateFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->stateCollection = $stateCollection;
        $this->stateLocaleCollection = $stateLocaleCollection;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->statelocale = $stateLocale;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * State access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_states');
    }
}
 