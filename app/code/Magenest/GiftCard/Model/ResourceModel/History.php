<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:38
 */

namespace Magenest\GiftCard\Model\ResourceModel;

class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * ResourceModel initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_giftcard_history', 'history_id');
    }
}
