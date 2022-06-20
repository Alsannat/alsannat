<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;

/**
 * Class Catalogcontrollerproductviewwe
 * @package Webengage\Event\Observer
 */
class Catalogcontrollerproductviewwe implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;

    /**
     * Catalogcontrollerproductviewwe constructor.
     * @param Data $helper
     */
    public function __construct(
        \Magento\Catalog\Helper\Image $image,
        Data $helper)
    {
        $this->helper = $helper;
        $this->image = $image;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this|void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (empty($_POST)) {
            $product = $observer->getEvent()->getProduct();
            $productId = $product->getId();
            $productImageUrl = $this->image->init($product, 'product_page_image_large')->getUrl();
            $OrgPrice = (float)$product->getPrice();

            $prepareJson = array(
                    'event_name' => 'Product Viewed',
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
}
