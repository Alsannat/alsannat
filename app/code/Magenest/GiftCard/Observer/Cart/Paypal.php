<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 22/03/2016
 * Time: 14:34
 */

namespace Magenest\GiftCard\Observer\Cart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Paypal implements ObserverInterface
{
    protected $_logger;
    public function __construct(
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_logger = $logger;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $cart \Magento\Payment\Model\Cart */
        $cart = $observer->getEvent()->getCart();

        $giftcard_amount = $cart->getSalesModel()->getDataUsingMethod('gift_cards_amount_used');
        $this->_logger->info('gift card amount '. $giftcard_amount);
        $cart->addDiscount($giftcard_amount);
    }
}
