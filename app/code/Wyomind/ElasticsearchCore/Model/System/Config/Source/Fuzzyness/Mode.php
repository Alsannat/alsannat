<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\System\Config\Source\Fuzzyness;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'AUTO', 'label' => __('AUTO')],
            ['value' => '0', 'label' => '0'],
            ['value' => '1', 'label' => '1'],
            ['value' => '2', 'label' => '2']
        ];
    }

    /**
     * Get options in "key-value" format
     * @return array
     */
    public function toArray()
    {
        return $this->toOptionArray();
    }
}