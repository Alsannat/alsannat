<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 11/03/2016
 * Time: 14:18
 */
namespace Magenest\GiftCard\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Address implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $quoteAddress \Magento\Quote\Model\Quote\Address */
        $quoteAddress = $observer->getEvent()->getQuoteAddress();

        /** @var  $quote  \Magento\Quote\Model\Quote */
       //$quote = $quoteAddress->getQuoteId();
        $quote = $quoteAddress->getQuote();
        $quoteAddress->setData('gift_cards_amount', $quote->getData('gift_cards_amount'));
        $quoteAddress->setData('base_gift_cards_amount', $quote->getData('base_gift_cards_amount'));
        $quoteAddress->setData('gift_cards_amount_used', $quote->getData('gift_cards_amount_used'));
        $quoteAddress->setData('base_gift_cards_amount_used', $quote->getData('base_gift_cards_amount_used'));
        $quoteAddress->setData('gift_cards', $quote->getData('gift_cards'));
        $quoteAddress->setData('grand_total', $quote->getData('grand_total'));
        $quoteAddress->setData('base_grand_total', $quote->getData('base_grand_total'));

        $quoteAddress->setData('base_grand_total_without_giftcard', $quote->getData('base_grand_total_without_giftcard'));
        $quoteAddress->setData('grand_total_without_giftcard', $quote->getData('grand_total_without_giftcard'));
    }
}
