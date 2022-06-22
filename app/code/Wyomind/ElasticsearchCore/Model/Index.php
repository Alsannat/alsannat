<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model;

class Index extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Wyomind\ElasticsearchCore\Model\ResourceModel\Index');
    }
    
    /**
     * The indexer ID is the unique identifier for this collection
     * 
     * @param string $indexerId
     * @return \Wyomind\ElasticsearchCore\Model\Index
     */
    public function loadByIndexerId($indexerId)
    {
        $collection = $this->getCollection()->addFieldToFilter('indexer_id', ['eq' => $indexerId]);
        
        return $collection->getFirstItem();
    }
}