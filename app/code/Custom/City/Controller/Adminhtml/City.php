<?php

namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\CityFactory;

abstract class City extends Action
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
     * @var \Custom\City\Model\CityFactory
     */
    protected $_cityFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param CityFactory $cityFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\File\Csv $csv
     * @param \Custom\City\Model\Resource\State\Collection $stateCollection
     * @param \Custom\City\Model\Resource\City\Collection $cityCollection
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        CityFactory $cityFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csv,
        \Custom\City\Model\Resource\State\Collection $stateCollection,
        \Custom\City\Model\Resource\City\Collection $cityCollection
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_cityFactory = $cityFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->stateCollection = $stateCollection;
        $this->cityCollection = $cityCollection;
    }

    /**
     * City access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_cities');
    }
}
 