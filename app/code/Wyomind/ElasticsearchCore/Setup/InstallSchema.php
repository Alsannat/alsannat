<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup, 
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        $installer = $setup;
        $installer->startSetup();
        
        // Drop table if exists
        $installer->getConnection()->dropTable($installer->getTable('wyomind_elasticsearchcore_index'));
        
        $table = $installer->getConnection()
                ->newTable($installer->getTable('wyomind_elasticsearchcore_index'))
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
                    ['nullable' => true, 'default' => null]
                )
                ->addColumn(
                    'update_mode',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => null]
                )
                ->addColumn(
                    'reindexed',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false, 'default' => 1]
                )
                ->addColumn(
                    'last_index_date', 
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, 
                    null, 
                    [
                        'nullable' => false,
                        'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE
                    ]
                );
        
        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
    }
}