<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 14:35
 */
namespace Magenest\GiftCard\Model\ResourceModel\GiftCardPurchased;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\GiftCardPurchased', 'Magenest\GiftCard\Model\ResourceModel\GiftCardPurchased');
    }
}
