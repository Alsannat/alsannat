<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class PaymentFeeTypes implements ArrayInterface
{
    const FIXED_AMOUNT = 0;
    const PERCENT = 1;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Fixed Amount'),
                'value' => self::FIXED_AMOUNT
            ],
            [
                'label' => __('Percent'),
                'value' => self::PERCENT
            ]
        ];
    }
}
