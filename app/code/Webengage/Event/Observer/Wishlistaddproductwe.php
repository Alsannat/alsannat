<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Wishlistaddproductwe implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;

    /**
     * Wishlistaddproductwe constructor.
     * @param \Magento\Catalog\Helper\Image $image
     * @param Data $helper
     */
    public function __construct(

        \Magento\Catalog\Helper\Image $image,
        Data $helper)
    {
        $this->helper = $helper;
        $this->image = $image;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {


        $event = $observer->getEvent();
        $product = $event->getProduct();
        $productId = $product->getId();
        $imageHelper = $this->image;
        $productImageUrl = $imageHelper->init($product, 'product_page_image_large')->getUrl();
        $OrgPrice = (float)$product->getPrice();
        $prepareJson = array(
                'event_name' => 'Added To Wishlist',
                'event_data' => array(
                    'productId' => $productId,
                    'productName' => $product->getName(),
                    'productDescription' => $product->getDescription(),
                    'productShortDescription' => $product->getShortDescription(),
                    'productSku' => $product->getSku(),
                    'productUrl' => $product->getProductUrl(),
                    'productImage' => $productImageUrl,
                    'productOriginalPrice' => $OrgPrice,
                    'categoryName' => $this->helper->getProductCategories($product)
                )
        );

        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/
        return $this;
    }
}
 
