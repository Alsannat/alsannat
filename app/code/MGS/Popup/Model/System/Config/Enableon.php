<?php

namespace MGS\Popup\Model\System\Config;

use Magento\Framework\Option\ArrayInterface;

class Enableon implements ArrayInterface
{
    const ALLPAGE = 1;
    const HOMEPAGE = 2;

    public function toOptionArray()
    {
        $options = [
            self::ALLPAGE => __('All Page'),
            self::HOMEPAGE => __('Only Home Page')
        ];
        return $options;
    }

    public static function getAvailableStatuses()
    {
        return [
            self::ALLPAGE => __('All Page')
            , self::HOMEPAGE => __('Only Home Page'),
        ];
    }
}
