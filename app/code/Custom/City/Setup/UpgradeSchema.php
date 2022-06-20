<?php namespace Custom\City\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();
        if (version_compare($context->getVersion(), '4.0.0') < 0) {
            $ziptableName = $installer->getTable('cities_zips');
            // Check if the table cities already exists
            if ($installer->getConnection()->isTableExists($ziptableName) != true) {

                $table = $installer->getConnection()
                    ->newTable($ziptableName)
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'unsigned' => true,
                            'nullable' => false,
                            'primary' => true
                        ],
                        'ID'
                    )
                    ->addColumn(
                        'zip_name',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Neighborhoods EN'
                    )
                    ->addColumn(
                        'zip_name_ar',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Neighborhoods Ar'
                    )
                    ->addColumn(
                        'city_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'City Id'
                    )
                    ->addColumn(
                        'state_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'State Id'
                    )
                    ->addColumn(
                        'country_id',
                        Table::TYPE_TEXT,
                        255,
                        [],
                        'Country Id'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_DATE,
                        null,
                        ['nullable' => false],
                        'Created At'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_SMALLINT,
                        null,
                        ['nullable' => false, 'default' => '0'],
                        'Status'
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['city_id']),
                        ['city_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['state_id']),
                        ['state_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['country_id']),
                        ['country_id']
                    )->addIndex(
                        $installer->getIdxName('cities_zips', ['zip_name']),
                        ['zip_name']
                    )
                    ->setComment('cities_zips Table')
                    ->setOption('type', 'InnoDB')
                    ->setOption('charset', 'utf8');
                $installer->getConnection()->createTable($table);
            } 
        }

        /* Custom table upgrade */
        if (version_compare($context->getVersion(), '4.0.2') < 0) {
            $tableName = $installer->getTable('cities');

            if ($installer->getConnection()->isTableExists($tableName) == true) {

                $installer->getConnection()
                ->addColumn($tableName, 'city_ar', [
                    'type'      => Table::TYPE_TEXT,
                    'length'    => 255,
                    'nullable'  => true,
                    'comment'   => 'Arabic city name',
                ]);

                $installer->getConnection()
                ->addColumn($tableName, 'sort_order', [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'City Sort',
                    'default' => 99,
                ]);
            }
        }

        if (version_compare($context->getVersion(), '4.0.3') < 0) {
            $tableName = $installer->getTable('cities');

            if ($installer->getConnection()->isTableExists($tableName) == true) {

                $installer->getConnection()
                ->addColumn($tableName, 'sort_order', [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => false,
                    'comment' => 'City Sort',
                    'default' => 99,
                ]);
            }
        }
        
    }

}
