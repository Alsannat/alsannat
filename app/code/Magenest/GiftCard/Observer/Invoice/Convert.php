<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 29/06/2017
 * Time: 14:40
 */

namespace Magenest\GiftCard\Observer\Invoice;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class Convert implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getTarget();
        $order = $observer->getEvent()->getSource();

        $subtotal = 0- floatval($order->getData('subtotal'));
        $baseSubtotal = 0- floatval($order->getData('base_subtotal'));

        $adjustData = [
            'grand_total' => $subtotal,
            'base_grand_total' => $baseSubtotal,
            'gift_cards_amount' => $order->getData('gift_cards_amount'),
            'base_gift_cards_amount' => $order->getData('base_gift_cards_amount'),
        ];
        $invoice->addData($adjustData);

        return $this;
    }
}