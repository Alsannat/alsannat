<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Salesquoteremoveitemwe implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;

    /**
     * Salesquoteremoveitemwe constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Image $image
     * @param Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Helper\Image $image,
        Data $helper)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
        $this->product = $product;
        $this->image = $image;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $eventData = $event->getData('quote_item');
        $productId = $eventData->getProductId();
        $product = $this->product->load($productId);
        $imageHelper = $this->image;
        $productImageUrl = $imageHelper->init($product, 'product_page_image_large')->getUrl();
        $OrgPrice = $product->getPrice();

        $prepareJson = array(
                'event_name' => 'Removed From Cart',
                'event_data' => array(
                    'productId' => $productId,
                    'productName' => $product->getName(),
                    'productDescription' => $product->getDescription(),
                    'productShortDescription' => $product->getShortDescription(),
                    'productSku' => $product->getSku(),
                    'productUrl' => $product->getProductUrl(),
                    'productImage' => $productImageUrl,
                    'productOriginalPrice' => (float)$OrgPrice,
                    'productQty' => (float)$eventData->getQty(),
                    'categoryName' => $this->helper->getProductCategories($product)
                )
        );
        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/
        return $this;
    }


}
