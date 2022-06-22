<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Listing\Indexes\Column;

class Reindexed implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get the options as array
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                ['label' => __('READY'), 'value' => 1],
                ['label' => __('REINDEX REQUIRED'), 'value' => 0]
            ];
        }

        return $this->options;
    }
}