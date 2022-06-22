<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Config\Source;

class Shipping implements \Magento\Framework\Option\ArrayInterface
{
    const ALL_SHIPPING = 0;
    const SPECIFIC_SHIPPING = 1;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('All Allowed Shipping Methods'),
                'value' => self::ALL_SHIPPING
            ],
            [
                'label' => __('Specific Shipping Methods'),
                'value' => self::SPECIFIC_SHIPPING
            ]
        ];
    }
}
