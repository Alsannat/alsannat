<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee;

use Amasty\CashOnDelivery\Model\PaymentFee;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(PaymentFee::class, PaymentFeeResource::class);
    }
}
