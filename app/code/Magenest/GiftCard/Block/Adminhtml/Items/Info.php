<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 23/07/2017
 * Time: 23:05
 */

namespace Magenest\GiftCard\Block\Adminhtml\Items;


class Info  extends  \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    public function getGiftCardAmount() {
        $parentBlock = $this->getParentBlock();
        $item = $parentBlock->getItem();

        $giftcardAmount = $item->getData('giftcard_amount');
        return $giftcardAmount;
    }

    public function getToHtml()
    {
        $amount = $this->getGiftCardAmount();
        if ($amount) {
            return $amount;
        } else {
            return '';
        }
    }
}