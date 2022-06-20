<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 14/04/2016
 * Time: 13:34
 */

namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class Price implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $product \Magento\Catalog\Model\Product */
        $product = $observer->getEvent()->getProduct();

        $productType =  $product->getTypeId();

        if ($productType === 'giftcard') {
            $scheme = intval($product->getData('giftcard_price_scheme'));
            $gcPrice =0;
            switch ($scheme) {
                case 0:
                    $fixedPrice = $product->getData('gc_fixed_price');
                    $gcPrice=$fixedPrice;
                    break;
                case 1:
                    $prices = $product->getData('giftcard_price_selector');
                    $pricesArr = explode(';', $prices);

                    //validate the price
                    if (count($pricesArr) > 0) {
                        $length = count($pricesArr) - 1;

                        foreach ($pricesArr as $key=>$priceNode) {
                            if ($priceNode =='' && $key == $length) continue;
                           $priceNode = floatval($priceNode);

                           if (!($priceNode > 0) ||!(is_numeric($priceNode)) )  {
                               throw new LocalizedException(__('You have to enter the valid price for gift card'));
                           }
                        }
                    } //end of validation
                    if (isset($pricesArr[0])) {
                        $gcPrice=$pricesArr[0];
                    }
                    break;
                case 2:
                    $minPrice = $product->getData('gc_min_price');
                    $maxPriceInput = $product->getData('gc_max_price');
                    $maxPrice = floatval($maxPriceInput);


                    if (!($maxPrice > 0) ||!(is_numeric($maxPrice)) )  {
                        throw new LocalizedException(__('You have to enter the valid max price for gift card'));
                    }
                    $gcPrice=$minPrice;
                    break;
            }

            $gcPrice = floatval($gcPrice);
            if ($gcPrice && is_numeric($gcPrice)) {
                echo "hi";exit;
                $product->setPrice($gcPrice);
            } else {
                throw new LocalizedException(__('You have to enter the valid price for gift card'));
            }
        }
    }
}
