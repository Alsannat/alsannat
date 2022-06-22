<?php

namespace MGS\Popup\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class PositionX implements ArrayInterface
{
    const LEFT = 'left';
    const RIGHT = 'right';
    const CENTER = 'center';

    public function toOptionArray()
    {
        $options = [
            self::LEFT => __('Left'),
            self::RIGHT => __('Right'),
            self::CENTER => __('Center')
        ];
        return $options;
    }
}
