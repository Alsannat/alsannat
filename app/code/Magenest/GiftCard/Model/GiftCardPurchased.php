<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 14:27
 */

namespace Magenest\GiftCard\Model;

class GiftCardPurchased extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING  = 0;
    const STATUS_AVAILABLE = 1;
    const STATUS_EXPIRED = 2;
    const STATUS_PENDING_PAYMENT = 3;
    const STATUS_PAYMENT_REVIEW = 4;

    const XML_PATH_ORDER_ITEM_STATUS ="giftcard/general/invoice_status_to_active";
    protected $_eventPrefix = 'magenest_giftcard_purchased';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'giftcard_purchased';
    /**
     * constructor method
     */
    public function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\ResourceModel\GiftCardPurchased');
    }
}
