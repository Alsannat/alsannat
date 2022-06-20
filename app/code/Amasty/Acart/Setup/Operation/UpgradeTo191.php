<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Api\Data\HistoryDetailInterface;
use Amasty\Acart\Api\Data\HistoryDetailInterfaceFactory;
use Amasty\Acart\Model\History\ProductDetails\DetailSaver;
use Amasty\Acart\Model\ResourceModel\History;
use Amasty\Acart\Model\ResourceModel\Quote;
use Amasty\Acart\Utils\BatchLoader;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeTo191
{
    /**
     * @var AddOpenedColumn
     */
    private $addOpenedColumn;

    /**
     * @var CreateProductDetailsTable
     */
    private $createProductDetailsTable;

    /**
     * @var History\CollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @var Quote\CollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var BatchLoader
     */
    private $batchLoader;

    /**
     * @var HistoryDetailInterfaceFactory
     */
    private $detailFactory;

    /**
     * @var DetailSaver
     */
    private $detailSaver;

    public function __construct(
        AddOpenedColumn $addOpenedColumn,
        CreateProductDetailsTable $createProductDetailsTable,
        History\CollectionFactory $historyCollectionFactory,
        Quote\CollectionFactory $quoteCollectionFactory,
        BatchLoader $batchLoader,
        HistoryDetailInterfaceFactory $detailFactory,
        DetailSaver $detailSaver
    ) {
        $this->addOpenedColumn = $addOpenedColumn;
        $this->createProductDetailsTable = $createProductDetailsTable;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->batchLoader = $batchLoader;
        $this->detailFactory = $detailFactory;
        $this->detailSaver = $detailSaver;
    }

    public function execute(SchemaSetupInterface $setup)
    {
        $this->addOpenedColumn->execute($setup);
        $this->createProductDetailsTable->execute($setup);
        $this->migrateQuoteProductData();
    }

    private function migrateQuoteProductData()
    {
        $historyCollection = $this->historyCollectionFactory->create();
        $historyCollection->addRuleQuoteData();
        $historyCollection->getSelect()->joinLeft(
            ['quote_table' => $historyCollection->getTable('quote')],
            'ruleQuote.quote_id = quote_table.entity_id',
            []
        );
        $historyCollection->addFieldToFilter('quote_table.entity_id', ['notnull' => true]);
        $historyCollection->getSelect()
            ->reset(Select::COLUMNS)
            ->columns(['quote_table.entity_id', 'main_table.history_id']);
        $quoteIdHistoryIdPairs = $historyCollection->getConnection()->fetchPairs($historyCollection->getSelect());

        if (!$quoteIdHistoryIdPairs) {
            return;
        }

        $quoteCollection = $this->quoteCollectionFactory->create();
        $quoteCollection->joinQuoteEmail();
        $quoteCollection->addFieldToFilter('quoteEmail.quote_id', ['in' => array_keys($quoteIdHistoryIdPairs)]);

        /** @var \Magento\Quote\Model\Quote $quote */
        foreach ($this->batchLoader->execute($quoteCollection) as $quote) {
            $historyId = $quoteIdHistoryIdPairs[$quote->getId()] ?? 0;

            if (!$historyId) {
                continue;
            }

            try {
                foreach ($quote->getAllItems() as $quoteItem) {
                    /** @var HistoryDetailInterface $detail */
                    $detail = $this->detailFactory->create();
                    $detail->setHistoryId((int)$historyId);
                    $detail->setProductName((string)$quoteItem->getName());
                    $detail->setProductPrice((float)$quoteItem->getPrice());
                    $detail->setProductSku((string)$quoteItem->getSku());
                    $detail->setProductQty((int)$quoteItem->getQty());
                    $detail->setStoreId((int)$quoteItem->getStoreId());
                    $detail->setCurrencyCode((string)$quote->getCurrency()->getQuoteCurrencyCode());
                    $this->detailSaver->execute($detail);
                }
            } catch (\Throwable $e) {
                null;
            }
        }
    }
}
