<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 02/03/2016
 * Time: 14:28
 */
namespace Magenest\GiftCard\Observer\Invoice;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Register implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();
        if ($invoice->getData('base_gift_cards_amount')) {
            $order->setBaseGiftCardsInvoiced($order->getData('base_gift_cards_invoiced') + $invoice->getData('base_gift_cards_amount'));
            $order->setGiftCardsInvoiced($order->getData('gift_cards_invoiced') + $invoice->getData('gift_cards_amount'));
        }
        return $this;
    }
}
