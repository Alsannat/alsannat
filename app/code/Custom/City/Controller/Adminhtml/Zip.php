<?php

namespace Custom\City\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Model\ZipFactory;

abstract class Zip extends Action
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
     * @var \Custom\City\Model\ZipFactory
     */
    protected $_zipFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param ZipFactory $zipFactory
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\File\Csv $csv
     * @param \Custom\City\Model\Resource\City\Collection $cityCollection
     * @param \Custom\City\Model\Resource\Zip\Collection $zipCollection
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        ZipFactory $zipFactory,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csv,
        \Custom\City\Model\Resource\City\Collection $cityCollection,
        \Custom\City\Model\Resource\Zip\Collection $zipCollection,
		\Custom\City\Model\Resource\State\Collection $stateCollection
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_zipFactory = $zipFactory;
        $this->directoryList = $directoryList;
        $this->csv = $csv;
        $this->cityCollection = $cityCollection;
        $this->zipCollection = $zipCollection;
		$this->stateCollection = $stateCollection;
    }

    /**
     * City access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_City::manage_zips');
    }
}
 