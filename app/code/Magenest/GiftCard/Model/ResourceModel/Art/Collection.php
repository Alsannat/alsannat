<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:39
 */
namespace Magenest\GiftCard\Model\ResourceModel\Art;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\Art', 'Magenest\GiftCard\Model\ResourceModel\Art');
    }
}
