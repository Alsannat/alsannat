<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 29/06/2016
 * Time: 11:03
 */
namespace Magenest\GiftCard\Controller\Design;

use Magento\Framework\App\ResponseInterface;

class Card extends \Magento\Framework\App\Action\Action
{
    protected $gcResultPageFactory;

    protected $backgroundFactory;

    protected $artFactory;

    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magenest\GiftCard\Model\BackgroundFactory $backgroundFactory
     * @param \Magenest\GiftCard\Model\ArtFactory $artFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magenest\GiftCard\Page\PageFactory $gcresultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\GiftCard\Model\BackgroundFactory $backgroundFactory,
        \Magenest\GiftCard\Model\ArtFactory $artFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magenest\GiftCard\Page\PageFactory $gcresultPageFactory
    ) {
        $this->_context = $context;
        $this->resultPageFactory = $resultPageFactory;
        $this->gcResultPageFactory = $gcresultPageFactory;

        $this->backgroundFactory = $backgroundFactory;
        $this->artFactory = $artFactory;
        $this->storeManager = $storeManagerInterface;

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
        $params = $this->getRequest()->getParams();

        $productId = $this->getRequest()->getParam('product');

        $background =  $this->getBackGround($productId);
        $art = $this->getArt($productId);
        $resultPage = $this->gcResultPageFactory->create(false, ['template'=>'Magenest_GiftCard::design.phtml']);

        $resultPage->assignPublic('productId', $productId);
        $resultPage->assignPublic('background', $background);
        $resultPage->assignPublic('art', $art);
        $background = $this->getBackGround($productId);

        $resultPage->getConfig()->getTitle()->prepend(__('Design Gift Card'));
        $update='<block class="Magento\Framework\View\Element\Text" name="foo" template="Magenest_GiftCard::test.phtml">';
        $resultPage->getLayout()->getUpdate()->addUpdate($update);
        return $resultPage;
    }

    /**
     * @param $productId
     * @return array
     */
    private function getBackGround($productId)
    {
        $out =[];
        $base_media_path = 'giftcard/template/background';

        $currentStore = $this->storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        $collection =  $this->backgroundFactory ->create()->getCollection()->addFieldToFilter('product_id', $productId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $bg) {
                $out[] = [
                    'title' => $bg->getTitle(),
                    'file' => $mediaUrl.$base_media_path.$bg->getFile()
                ];
            }
        }

        return $out;
    }

    /**
     * @param $productId
     * @return array
     */
    private function getArt($productId)
    {
        $out =[];
        $base_media_path = 'giftcard/template/background';

        $currentStore = $this->storeManager->getStore();
        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $collection =  $this->artFactory ->create()->getCollection()->addFieldToFilter('product_id', $productId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $bg) {
                $out[] = [
                    'title' => $bg->getTitle(),
                    'file' => $mediaUrl.$base_media_path.$bg->getFile()
                ];
            }
        }

        return $out;
    }
}
