<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/02/2016
 * Time: 10:52
 */

namespace Magenest\GiftCard\Model\Product\Type;
use Magento\Framework\App\ObjectManager;
class Price extends \Magento\Catalog\Model\Product\Type\Price
{
    /**
     * Get product final price
     *
     * @param   float                     $qty
     * @param   \Magento\Catalog\Model\Product $product
     * @return  float
     */
    public function getFinalPrice($qty, $product)
    {

        if ($qty === null && $product->getCalculatedFinalPrice() !== null) {
            return $product->getCalculatedFinalPrice();
        }

        $basePrice =  $this->getBasePrice($product, $qty);
        $finalPrice = $this->getBasePrice($product, $qty);


       
        $product->setFinalPrice($finalPrice);
        $this->_eventManager->dispatch('catalog_product_get_final_price', ['product' => $product, 'qty' => $qty]);
        $finalPrice = $product->getData('final_price');

        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);

        //there are three price scheme
        $priceScheme = (int)$product->getData('giftcard_price_scheme');

        if (!$priceScheme) {
            //in case the product price function invoked by \Magento\Quote\Model\Quote\Address\Total\product
            /** @var \Magento\Quote\Model\Quote\Item\Option $buyRequest */
            $buyRequest = $product->getCustomOption('info_buyRequest');

            if (is_object($buyRequest)) {
                $buyRequestVal = unserialize($buyRequest->getValue());

                if (isset($buyRequestVal['giftcard_price_scheme'])){
                    $priceScheme = $buyRequestVal['giftcard_price_scheme'];
                } elseif(isset($buyRequestVal['giftcard']['giftcard_amount'])) {
                    $priceScheme = 4;
                }
            }

        }

        if ($priceScheme != 0)
        {
            $finalPrice += ($this->applyCustomerSelectedPrice($product, $qty) - $basePrice);
        }

        $product->setFinalPrice($finalPrice);
        return max(0, $product->getData('final_price'));
    }

    /**
     * @param  \Magento\Catalog\Model\Product $product
     * @param $qty  int

     */
    public function applyCustomerSelectedPrice($product, $qty)
    {

        $options = $product->getCustomOption('additional_options');
        if ($options) {
            $additionalOption = unserialize($options->getValue());
            if ($additionalOption) {
                foreach ($additionalOption as $key => $value) {
                    if ($value['label'] == __('giftcard_amount')) {
                        return $value['value'];
                    }
                }
            }
         // return  $additionalOption['giftcard_amount'];
        }
    }
}