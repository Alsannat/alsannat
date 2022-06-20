<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/08/2016
 * Time: 16:17
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Design;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\Filesystem\DirectoryList;

class Upload extends \Magento\Backend\App\Action
{
    protected $_uploader;

    protected $_filesystem;

    protected $_logger;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    public function __construct(
        Logger $logger,
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\File\UploaderFactory $uploader,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->_logger = $logger;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_adapterFactory = $adapterFactory;
        $this->_uploader = $uploader;
        $this->_filesystem = $filesystem;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_objectManager = $context->getObjectManager();
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $uploader = $this->_uploader->create(['fileId' => 'link']);
        $base_media_path = 'giftcard/template/background';

        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $mediaDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory->getAbsolutePath($base_media_path));

        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $resultJson->setData($result);
        return $resultJson;
    }
}
