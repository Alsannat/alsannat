<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\Formatter;

class Decimal
{
    /**
     * @param mixed $value
     * @param mixed $store
     * @return mixed
     */
    public function format($value, $store = null)
    {
        if (strpos($value, ',') !== false) {
            $value = array_unique(array_map('floatval', explode(',', $value)));
        } else {
            $value = (float) $value;
        }

        return $value;
    }
}