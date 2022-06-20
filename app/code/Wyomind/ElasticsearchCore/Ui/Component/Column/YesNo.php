<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Column;

/**
 * Not used anymore
 * @package Wyomind\ElasticsearchCore\Ui\Component\Column
 */
class YesNo implements \Magento\Framework\Data\OptionSourceInterface
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
                ['label' => __('Yes'), 'value' => \Wyomind\ElasticsearchCore\Helper\Indexer::ACTIVE],
                ['label' => __('No'), 'value' => \Wyomind\ElasticsearchCore\Helper\Indexer::UNACTIVE]
            ];
        }
        
        return $this->options;
    }
}