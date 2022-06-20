<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Catalogproductcompareaddproductwe implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;
    private $helper;

    /**
     * Catalogproductcompareaddproductwe constructor.
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
                'event_name' => 'Added For Comparison',
                'event_data' => array(
                    'productId' => $productId,
                    'productName' => $product->getName(),
                    'productDescription' => $product->getDescription(),
                    'productShortDescription' => $product->get(),
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
