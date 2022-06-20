<?php
namespace Custom\Contest\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\Block\Widget\Grid\ExportInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportXml extends Action {

	/**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

	/**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Export customers most ordered report to CSV format
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $fileName = 'contest.xml';
        /** @var ExportInterface $exportBlock */ 
        $exportBlock = $this->_view->getLayout()->getBlock('adminhtml.contest.grid.export');

        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getXml(),
            DirectoryList::VAR_DIR
        );
    }
}