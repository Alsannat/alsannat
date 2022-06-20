<?php

namespace MGS\Popup\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class PositionY implements ArrayInterface
{
    const TOP = 'top';
    const BOTTOM = 'bottom';
    const MIDDLE = 'middle';

    public function toOptionArray()
    {
        $options = [
            self::TOP => __('Top'),
            self::BOTTOM => __('Bottom'),
            self::MIDDLE => __('Middle')
        ];
        return $options;
    }
}
