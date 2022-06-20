<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Cron;

/**
 * Class UpdateAllIndexes
 */
class UpdateAllIndexes
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Index
     */
    protected $_indexModel = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\ToReindexFactory
     */
    protected $_toReindexModelFactory = null;

    /**
     * Class constructor
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     * @param \Wyomind\ElasticsearchCore\Model\Index $indexModel
     * @param \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory,
        \Wyomind\ElasticsearchCore\Model\Index $indexModel,
        \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory
    )
    {
        $this->_coreDate = $coreDate;
        $this->_indexerHelperFactory = $indexerHelperFactory;
        $this->_indexModel = $indexModel;
        $this->_toReindexModelFactory = $toReindexModelFactory;
    }

    /**
     * @param \Magento\Cron\Model\Schedule $schedule
     */
    public function execute(\Magento\Cron\Model\Schedule $schedule)
    {
        $indexers = $this->_indexerHelperFactory->create()->getAllIndexers();

        foreach ($indexers as $indexer) {
            $type = $indexer->getType();

            /** @var \Wyomind\ElasticsearchCore\Model\ToReindex $toReindexModel */
            $toReindexModel = $this->_toReindexModelFactory->create();
            $indexerLastEntries = $toReindexModel->getIndexerLastEntries($type);

            foreach ($indexerLastEntries as $toReindex) {
                if ($toReindex['last_entry'] > $indexer->getLastIndexDate()) {
                    $indexer->executeRow($toReindex['to_reindex']);
                }
            }

            $index = $this->_indexModel->loadByIndexerId($type);
            $index->setIndexerId($type);
            $index->setUpdateMode('schedule');
            $index->setReindexed(1);
            $datetime = $this->_coreDate->date('Y-m-d H:i:s', $this->_coreDate->gmtTimestamp());
            $index->setLastIndexDate($datetime);
            $index->save();

            // remove the indexer lines in the "buffer" table wyomind_elasticsearchcore_to_reindex
            $toReindexModel->deleteIndexerToReindex($type);
        }
    }
}