<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 31/08/2016
 * Time: 09:06
 */

namespace Magenest\GiftCard\Model\ResourceModel;

class Background extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * ResourceModel initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_giftcard_background', 'id');
    }

    /**
     * @param $productId
     * @return array
     */
    public function getBackGroundByProductId($productId)
    {
        $adapter   = $this->_getConnection('read');
        $mainTable = $this->getTable('magenest_giftcard_background');
        $select = $adapter->select()->from(
            ['m' => $mainTable],
            'm.id'
        )->where('m.product_id = ? ', $productId);

        $row = $adapter->fetchAssoc($select);

        return $row;
    }
}
