<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/06/2016
 * Time: 20:06
 */

namespace Magenest\GiftCard\Controller\Design;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $filesystem;

    /**
     * @var \Magento\Framework\File\UploaderFactory
     */
    protected $uploader;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $quoteItemFactory;

    protected $checkoutSession;

    /** @var \Magento\Framework\Filesystem\Io\File  */
    protected $ioFile;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Image\AdapterFactory $adapterFactory,
        \Magento\Framework\File\UploaderFactory $uploader,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\GiftCard\Logger\Logger $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {

        $this->_context = $context;
        $this->resultPageFactory = $resultPageFactory;
        $this->filesystem = $filesystem;
        $this->uploader = $uploader;

        $this->ioFile = $ioFile;
        $this->logger = $logger;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->checkoutSession = $checkoutSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $image = $this->getRequest()->getParam('image');
        $quoteId = $this->getRequest()->getParam('quoteId');
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $result = [];

        $itemId = (int)$this->checkoutSession->getLastAddedItemId();

        try {
            $data = base64_decode($image);

            $this->varDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);

            $mediaDirectory = $this->filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
            $giftcard_path = 'giftcard/template'. 'giftcard_'.$quoteId.'_'.$itemId .'.png';

            $giftCardUploadFile = $mediaDirectory->getAbsolutePath($giftcard_path);

            $uploadDir = $mediaDirectory->getAbsolutePath('giftcard/template');

            if (!@is_dir($uploadDir)) {
                $this->ioFile->mkdir($uploadDir ,0777);
            }
            
            //  file_put_contents($uploadDir, $data);
            $img = imagecreatefromstring($data);

            if ($img) {
                imagepng($img, $giftCardUploadFile, 0);
                if (\Magenest\GiftCard\Helper\Data::ENABLE_DEBUG ) {

                    $this->logger->addDebug('the personal design gift card path');
                    $this->logger->addDebug($giftCardUploadFile);
                }
                /** @var \Magento\Quote\Model\Quote $quote */
                $quote = $this->checkoutSession->getQuote();
                $items = $quote->getAllVisibleItems();

                foreach ($items as &$quoteItem) {
                    if ($quoteItem->getId() == $itemId) {
                        $previousAddtionData = $quoteItem->getAdditionalData();
                        //$quoteItem->get

                        $quoteItem->setAdditionalData($previousAddtionData.'giftcard_image'.$giftCardUploadFile);
                        $additionalOptions=[];
                        $additionalOptions[] = ['label'=>'file_path' , 'value' =>$giftCardUploadFile];

                        /** @var  $buyRequestOption \Magento\Quote\Model\Quote\Item\Option */
                        $quoteItem->addOption(array (
                            'code' => 'giftcard_design',
                            'product_id' => $quoteItem->getProduct()->getId(),
                            'value' => serialize($additionalOptions)
                        ));
                        $quoteItem->save();
                        $result['code'] = 'success';
                    }
                }
            } else {
                $result['code'] = 'error';
            }
        } catch (\Exception $e) {
            $this->logger->addError($e->getMessage());
            $result['code'] = 'error';
        }

        $resultJson->setData($result);
        return $resultJson;
    }
}
