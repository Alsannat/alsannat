<?php
declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\GuestCustomerQuotes;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateGuestCustomerQuotesTable
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(GuestCustomerQuotes::TABLE_NAME);
        $customerTable = $setup->getTable('customer_entity');
        $quoteTable = $setup->getTable('quote');

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Acart Guest Customer Quotes Table'
            )->addColumn(
                'customer_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Customer Id'
            )->addColumn(
                'quote_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Quote Id'
            )->addColumn(
                'orig_quote_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Original Quote Id'
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'customer_id',
                    $customerTable,
                    'entity_id'
                ),
                'customer_id',
                $customerTable,
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'quote_id',
                    $quoteTable,
                    'entity_id'
                ),
                'quote_id',
                $quoteTable,
                'entity_id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    'orig_quote_id',
                    $quoteTable,
                    'entity_id'
                ),
                'orig_quote_id',
                $quoteTable,
                'entity_id',
                Table::ACTION_CASCADE
            );
    }
}
