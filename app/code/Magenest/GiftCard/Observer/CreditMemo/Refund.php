<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 02/03/2016
 * Time: 14:36
 */
namespace Magenest\GiftCard\Observer\CreditMemo;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Refund implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $creditmemo  \Magento\Sales\Api\Data\CreditmemoInterface */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        if ($creditmemo->getBaseGiftCardsAmount()) {
            if ($creditmemo->getRefundGiftCards()) {
                $baseAmount = $creditmemo->getBaseGiftCardsAmount();
                $amount = $creditmemo->getGiftCardsAmount();

                $creditmemo->setBsCustomerBalTotalRefunded($creditmemo->getBsCustomerBalTotalRefunded() + $baseAmount);
                $creditmemo->setCustomerBalTotalRefunded($creditmemo->getCustomerBalTotalRefunded() + $amount);
            }

            $order->setBaseGiftCardsRefunded(
                $order->getBaseGiftCardsRefunded() + $creditmemo->getBaseGiftCardsAmount()
            );
            $order->setGiftCardsRefunded($order->getGiftCardsRefunded() + $creditmemo->getGiftCardsAmount());

            if ($order->getGiftCardsInvoiced() > 0 &&
                $order->getGiftCardsInvoiced() == $order->getGiftCardsRefunded()
            ) {
                $order->setForcedCanCreditmemo(false);
            }
        }

        return $this;
    }
}
