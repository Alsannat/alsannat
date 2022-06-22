<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class CalculateBasedOn implements ArrayInterface
{
    const EXCLUDING_TAX = 0;
    const INCLUDING_TAX = 1;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Cart Subtotal Excluding Tax'),
                'value' => self::EXCLUDING_TAX
            ],
            [
                'label' => __('Cart Subtotal Including Tax'),
                'value' => self::INCLUDING_TAX
            ]
        ];
    }
}
