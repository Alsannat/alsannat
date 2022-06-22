<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $installer = $setup;
            $installer->startSetup();

            // Drop table if exists
            $installer->getConnection()->dropTable($installer->getTable('wyomind_elasticsearchcore_to_reindex'));

            $table = $installer->getConnection()
                ->newTable($installer->getTable('wyomind_elasticsearchcore_to_reindex'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    11,
                    ['primary' => true, 'identity' => true, 'unsigned' => true, 'nullable' => false]
                )
                ->addColumn(
                    'indexer_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'to_reindex',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false]
                )
                ->addColumn(
                    'created_at',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
                    ]
                );

            $installer->getConnection()->createTable($table);

            $installer->endSetup();
        }
    }
}