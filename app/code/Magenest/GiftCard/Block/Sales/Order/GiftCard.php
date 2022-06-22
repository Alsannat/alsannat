<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 16/03/2016
 * Time: 14:47
 */

namespace Magenest\GiftCard\Block\Sales\Order;

use Magento\Sales\Model\Order;

class GiftCard extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Order
     */
    protected $_order;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;
    /**
     * Initialize all order totals relates with tax
     *
     * @return \Magento\Tax\Block\Sales\Order\Tax
     */
    public function initTotals()
    {
        /** @var $parent \Magento\Sales\Block\Adminhtml\Order\Invoice\Totals */
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $object = new \Magento\Framework\DataObject([
            'code' => 'giftcard',
            'field' => 'giftcard_amount',
            'value' => $this->_source->getData('gift_cards_amount'),
            'label' => __('Gift Card'),
        ]);
        $parent->addTotal($object, 'tax');
        return $this;
    }
}
