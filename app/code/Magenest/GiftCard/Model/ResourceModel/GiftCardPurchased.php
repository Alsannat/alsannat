<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 14:33
 */

namespace Magenest\GiftCard\Model\ResourceModel;

class GiftCardPurchased extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * ResourceModel initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_giftcard_purchased', 'giftcard_purchased_id');
    }
}
