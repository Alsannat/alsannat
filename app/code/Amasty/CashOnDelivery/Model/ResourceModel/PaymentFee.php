<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\ResourceModel;

use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class PaymentFee extends AbstractDb
{
    const TABLE_NAME = 'amasty_cash_on_delivery_fee_quote';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, PaymentFeeInterface::ENTITY_ID);
    }
}
