<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 09/03/2016
 * Time: 11:30
 */
namespace Magenest\GiftCard\Block\Sales\Order;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * Get totals source object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Create the weee ("FPT") totals summary
     *
     * @return $this
     */
    public function initTotals()
    {
        /** @var $items \Magento\Sales\Model\Order\Item[] */
        $items = $this->getSource()->getAllItems();
        $store = $this->getSource()->getStore();

/*        $weeeTotal = $this->weeeData->getTotalAmounts($items, $store);*/
         $source = $this->getSource();
         $giftCardTotal = $this->getSource()->getData('gift_cards_amount');
        if ($giftCardTotal) {
            // Add our total information to the set of other totals
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $this->getNameInLayout(),
                    'label' => __('GiftCard'),
                    'value' => $giftCardTotal,
                ]
            );
            if ($this->getBeforeCondition()) {
                $this->getParentBlock()->addTotalBefore($total, $this->getBeforeCondition());
            } else {
                $this->getParentBlock()->addTotal($total, $this->getAfterCondition());
            }
        }
        return $this;
    }
}
