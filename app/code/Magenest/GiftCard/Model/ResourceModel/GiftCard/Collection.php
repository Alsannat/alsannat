<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:34
 */
namespace Magenest\GiftCard\Model\ResourceModel\GiftCard;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'giftcard_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\GiftCard', 'Magenest\GiftCard\Model\ResourceModel\GiftCard');
    }

    public function getCardNeedToBeSent()
    {
        $current_date_time = new \DateTime();

        $currentTime = $current_date_time->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
        $cond        = 'available_date < '."'$currentTime' and is_send !=1";
        $select      = $this->getSelect()->where($cond);
        $this->addFieldToFilter('status', 0);
        $this->setOrder('created_at', self::SORT_ORDER_ASC);

        return $this;
    }
}
