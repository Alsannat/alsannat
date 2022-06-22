<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\System\Config\Source\Query;

class Operator implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'OR', 'label' => __('OR')],
            ['value' => 'AND', 'label' => __('AND')]
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return ['OR' => __('OR'), 'AND' => __('AND')];
    }
}