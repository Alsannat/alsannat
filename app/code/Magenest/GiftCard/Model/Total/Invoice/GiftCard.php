<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/11/2015
 * Time: 16:54
 */
namespace Magenest\GiftCard\Model\Total\Invoice;

use \Magento\Sales\Model\Order\Total\AbstractTotal;

class GiftCard extends \Magento\Sales\Model\Order\Invoice\Total\AbstractTotal
{
    /**
     * GiftCard data
     *
     * @var \Magenest\GiftCard\Helper\Data
     */
    protected $_giftcardData = null;
    public function __construct(\Magenest\GiftCard\Helper\Data $giftcardData, array $data = [])
    {
        $this->_giftcardData = $giftcardData;
        parent::__construct($data);
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     *  @return $this

     */
    public function collect(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $store = $invoice->getStore();
        $order = $invoice->getOrder();
        $order = $invoice->getOrder();
        if ($order->getBaseGiftCardsAmount() && $order->getBaseGiftCardsInvoiced() != $order->getBaseGiftCardsAmount()) {
            $gcaLeft = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
            $used = 0;
            $baseUsed = 0;
            if ($gcaLeft >= $invoice->getBaseGrandTotal()) {
                $baseUsed = $invoice->getBaseGrandTotal();
                $used = $invoice->getGrandTotal();

                $invoice->setBaseGrandTotal(0);
                $invoice->setGrandTotal(0);
            } else {
                $baseUsed = $order->getBaseGiftCardsAmount() - $order->getBaseGiftCardsInvoiced();
                $used = $order->getGiftCardsAmount() - $order->getGiftCardsInvoiced();

                $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
                $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            }

            $invoice->setBaseGiftCardsAmount($baseUsed);
            $invoice->setGiftCardsAmount($used);
        }
        return $this;
    }
}
