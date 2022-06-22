<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 15/03/2016
 * Time: 11:53
 */

namespace Magenest\GiftCard\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Order implements ObserverInterface
{
    /** @var  \Magenest\GiftCard\Model\GiftCardFactory */
    protected $_quoteFactory;
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    ) {
    
        $this->_quoteFactory = $quoteFactory;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $order \Magento\Sales\Model\Order */

        $order = $observer->getEvent()->getOrder();
        $quoteId = $order->getQuoteId();

        $quote = $this->_quoteFactory->create()->load($quoteId);
        $giftcards = $quote->getData('gift_cards');
        $gift_cards_amount_used = $quote->getData('gift_cards_amount_used');
        $base_gift_cards_amount_used = $quote->getData('base_gift_cards_amount_used');
        /** @var  $order \Magento\Sales\Model\Order */
        $order = $observer->getEvent()->getOrder();

        $order->setData('gift_cards', $giftcards);
        $order->setData('gift_cards_amount', $gift_cards_amount_used);
        $order->setData('base_gift_cards_amount', $base_gift_cards_amount_used);
    }
}
