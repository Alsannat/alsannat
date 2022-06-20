<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Listing\Indexes\Column;

class UpdateMode implements \Magento\Framework\Data\OptionSourceInterface
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
                ['label' => __('On Save'), 'value' => \Wyomind\ElasticsearchCore\Helper\Indexer::UPDATE_MODE_ON_SAVE],
                ['label' => __('By Schedule'), 'value' => \Wyomind\ElasticsearchCore\Helper\Indexer::UPDATE_MODE_BY_SCHEDULE]
            ];
        }
        
        return $this->options;
    }
}