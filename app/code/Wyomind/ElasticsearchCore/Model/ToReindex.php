<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model;

class ToReindex extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Wyomind\ElasticsearchCore\Model\ResourceModel\ToReindex');
    }
    
    /**
     * The indexer ID is the unique identifier for this collection
     * 
     * @param string $indexerId
     * @return \Wyomind\ElasticsearchCore\Model\ToReindex
     */
    public function loadByIndexerId($indexerId)
    {
        $collection = $this->getCollection()->addFieldToFilter('indexer_id', ['eq' => $indexerId]);

        return $collection;
    }

    /**
     * Get the id to reindex and the last entry created_at
     *
     * @param string $indexerId
     * @return array
     */
    public function getIndexerLastEntries($indexerId)
    {
        $entries = $this->loadByIndexerId($indexerId)
                            ->addFieldToSelect('to_reindex')
                            ->addFieldToSelect(new \Zend_Db_Expr('MAX(created_at) AS last_entry'))
                            ->getSelect()->group('to_reindex')
                            ->query()->fetchAll();

        return $entries;
    }

    /**
     * Delete all entries related to an indexer type
     *
     * @param $indexerId
     */
    public function deleteIndexerToReindex($indexerId)
    {
        $collection = $this->loadByIndexerId($indexerId);

        foreach ($collection as $line) {
            $line->delete();
        }
    }
}