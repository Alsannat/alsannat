<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\Formatter;

class Standard
{
    /**
     * @param mixed $value
     * @param mixed $store
     * @return mixed
     */
    public function format($value, $store = null)
    {
        return $value;
    }
}