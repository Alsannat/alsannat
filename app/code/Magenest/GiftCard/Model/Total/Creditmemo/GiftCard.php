<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/11/2015
 * Time: 16:53
 */
namespace Magenest\GiftCard\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;

class GiftCard extends \Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal
{
    public function collect(Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getBaseGiftCardsAmount() && $order->getBaseGiftCardsInvoiced() != 0) {
            $gcaLeft = $order->getBaseGiftCardsInvoiced() - $order->getBaseGiftCardsRefunded();

            $used = 0;
            $baseUsed = 0;

            if ($gcaLeft >= $creditmemo->getBaseGrandTotal()) {
                $baseUsed = $creditmemo->getBaseGrandTotal();
                $used = $creditmemo->getGrandTotal();

                $creditmemo->setBaseGrandTotal(0);
                $creditmemo->setGrandTotal(0);

                $creditmemo->setAllowZeroGrandTotal(true);
            } else {
                $baseUsed = $order->getBaseGiftCardsInvoiced() - $order->getBaseGiftCardsRefunded();
                $used = $order->getGiftCardsInvoiced() - $order->getGiftCardsRefunded();

                $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
                $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            }

            $creditmemo->setBaseGiftCardsAmount($baseUsed);
            $creditmemo->setGiftCardsAmount($used);
        }

        $creditmemo->setBaseCustomerBalanceReturnMax($creditmemo->getBaseCustomerBalanceReturnMax() + $creditmemo->getBaseGiftCardsAmount());

        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getGiftCardsAmount());
        $creditmemo->setCustomerBalanceReturnMax($creditmemo->getCustomerBalanceReturnMax() + $creditmemo->getGrandTotal());

        return $this;
    }
}
