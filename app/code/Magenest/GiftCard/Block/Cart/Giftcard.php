<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/01/2016
 * Time: 06:18
 */
namespace Magenest\GiftCard\Block\Cart;

class Giftcard extends \Magento\Checkout\Block\Cart\AbstractCart
{
    public function getGiftCard()
    {
        $out = [];
        $giftcards = unserialize($this->getQuote()->getData('gift_cards'));

        if ($giftcards) {
            foreach ($giftcards as $giftcard) {
                if ($giftcard['code']) {
                    $out[]=$giftcard;
                }
            }
        }
        return $out;
    }
}
