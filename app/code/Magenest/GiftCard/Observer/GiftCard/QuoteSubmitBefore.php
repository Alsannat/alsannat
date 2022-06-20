<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/02/2016
 * Time: 14:00
 */
namespace Magenest\GiftCard\Observer\GiftCard;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    /** @var  \Magenest\GiftCard\Model\GiftCardFactory */
    protected $giftCardFactory;
    public function __construct(
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory
    ) {
    
        $this->giftCardFactory = $giftCardFactory;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $giftCards = unserialize($quote->getData('gift_cards'));

        if ($giftCards) {
            foreach ($giftCards as $giftCard) {

                /** @var  $giftCardBean \Magenest\GiftCard\Model\GiftCard */
                $giftCardBean = $this->giftCardFactory->create();
                if (isset($giftCard['used']) && isset($giftCard['code'])) {
                    $giftCardBean->loadByCode($giftCard['code'])->payForOrder($giftCard['used'], $quote, $order);
                }
            }
        }
    }
}
