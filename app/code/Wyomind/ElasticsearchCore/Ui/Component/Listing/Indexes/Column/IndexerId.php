<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Listing\Indexes\Column;

class IndexerId implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer
     */
    protected $_indexerHelper = null;
    
    /**
     * @var array 
     */
    public $options = null;
    
    /**
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
    )
    {
        $this->_indexerHelper = $indexerHelper;
    }
    
    /**
     * Get all options available
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $collection = $this->_indexerHelper->getTypes();
            foreach ($collection as $type) {
                $this->options[] = [
                    'label' => $type, 'value' => $type
                ];
            }
            $this->options = array_map('unserialize', array_unique(array_map('serialize', $this->options)));
        }
        
        return $this->options;
    }
}