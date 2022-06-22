<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 16/03/2016
 * Time: 13:54
 */
namespace Magenest\GiftCard\Observer\View;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Order implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $totals \Magento\Sales\Block\Order\Totals */
        $totals = $observer->getEvent()->getBlock();
        if (get_class($totals) == 'Magento\Sales\Block\Order\Totals') {
            $object = new \Magento\Framework\DataObject([
                'code' => 'giftcard',
                'field' => 'giftcard_amount',
                'value' => $totals->getSource()->getData('gift_cards_amount'),
                'label' => __('Gift Card'),
            ]);
           // $totals->addTotal($object, 'shipping');
        }
    }
}
