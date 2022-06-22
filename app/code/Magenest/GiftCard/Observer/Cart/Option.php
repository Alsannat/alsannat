<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 10:28
 */
namespace Magenest\GiftCard\Observer\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;

class Option implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        /** @var \Magento\Quote\Model\Quote\Item $item */
        $item = $observer->getEvent()->getQuoteItem();

        $productType = $item->getProductType();

        if ($productType == 'giftcard') {
            $buyInfo = $item->getBuyRequest();

            /** @var  $product  \Magento\Catalog\Model\Product */
            $product = $observer->getEvent()->getProduct();


            $giftcardInfomation = [
                'giftcard_amount',
                'giftcard_template',
                'giftcard_recipient_name',
                'giftcard_recipient_email',
                'giftcard_sender_name',
                'giftcard_sender_email',
                'giftcard_headline',
                'giftcard_message',
                'giftcard_schedule_send_time'
            ];
            $additionalOptions = array ();
            if ($additionalOption = $item->getOptionByCode('additional_options')) {
                if ($additionalOption)
                $additionalOptions = (array)unserialize($additionalOption->getValue());
            }
            $giftCardInfo = $buyInfo->getData('giftcard');

            //if the gift card amount is not set then it equals to the gift card product price

            if (!isset($giftCardInfo['giftcard_amount'])) $giftCardInfo['giftcard_amount'] = $product->getPrice();

            //default pdf template

            $productOption = array();
            foreach ($giftcardInfomation as $key => $value) {
                if (isset($giftCardInfo[$value])) {
                    $optionLabel = $value ;

                    $optionValue = $giftCardInfo[$value];
                    $additionalOptions[] = ['label'=>$optionLabel , 'value' =>$optionValue];
                    $productOption[]=['label'=>$optionLabel , 'value' =>$optionValue];
                }
            }

            //only add option to item

            if (is_array($additionalOptions) && !empty($additionalOptions)) {
                $item->addOption(array (
                'code' => 'additional_options',
                 'product_id' => $product->getId(),
                'value' =>serialize($additionalOptions)
                ));
            }

            $optionsProduct = $product->getCustomOption('additional_options');

            if (!$optionsProduct) {
                $product->addCustomOption('additional_options', serialize($productOption));
            }
        }
    }
}
