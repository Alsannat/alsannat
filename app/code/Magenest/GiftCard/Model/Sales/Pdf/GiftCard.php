<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 09/03/2016
 * Time: 12:02
 */
namespace Magenest\GiftCard\Model\Sales\Pdf;

class GiftCard extends \Magento\Sales\Model\Order\Pdf\Total\DefaultTotal
{
    public function getTotalsForDisplay()
    {
        /** @var $items \Magento\Sales\Model\Order\Item[] */
        $items = $this->getSource()->getAllItems();
        $store = $this->getSource()->getStore();

        $giftCardTotal = $this->getSource()->getData('gift_cards_amount');

        // If we have no giftcard, check if we still need to display it
        if (!$giftCardTotal && !filter_var($this->getDisplayZero(), FILTER_VALIDATE_BOOLEAN)) {
            return [];
        }

        // Display the Weee total amount
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $totals = [
            [
                'amount' => $this->getOrder()->formatPriceTxt($giftCardTotal),
                'label' => __($this->getTitle()) . ':',
                'font_size' => $fontSize,
            ],
        ];

        return $totals;
    }
}
