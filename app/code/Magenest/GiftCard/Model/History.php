<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:25
 */

namespace Magenest\GiftCard\Model;

class History extends \Magento\Framework\Model\AbstractModel
{
    const PAY_FOR_ORDER = 1;
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_giftcard_history';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'giftcard_history';


    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\ResourceModel\History');
    }
}
