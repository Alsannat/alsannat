<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\History;
use Amasty\Acart\Model\History\ProductDetails\Detail;
use Amasty\Acart\Model\History\ProductDetails\ResourceModel\Detail as DetailResource;
use Amasty\Acart\Model\ResourceModel\History as HistoryResource;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateProductDetailsTable
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(DetailResource::TABLE_NAME);
        $historyTable = $setup->getTable(HistoryResource::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Acart History Product Details Table'
            )->addColumn(
                Detail::DETAIL_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Detail Id'
            )->addColumn(
                Detail::HISTORY_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Associated History Id'
            )->addColumn(
                Detail::PRODUCT_NAME,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Product Name'
            )->addColumn(
                Detail::PRODUCT_SKU,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Product SKU'
            )->addColumn(
                Detail::PRODUCT_PRICE,
                Table::TYPE_DECIMAL,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Price'
            )->addColumn(
                Detail::PRODUCT_QTY,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Product Qty'
            )->addColumn(
                Detail::STORE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Associated Store ID to quote item'
            )->addColumn(
                Detail::CURRENCY_CODE,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Associated currency code to quote item'
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    Detail::HISTORY_ID,
                    $historyTable,
                    History::HISTORY_ID
                ),
                Detail::HISTORY_ID,
                $historyTable,
                History::HISTORY_ID,
                Table::ACTION_CASCADE
            );
    }
}
