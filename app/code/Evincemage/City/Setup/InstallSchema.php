<?php

namespace Evincemage\City\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('courier_manager')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Item ID'
        )->addColumn(
            'city_ar',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        )->addColumn(
            'city_en',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City Arabic'
        )->addColumn(
            'naquel_city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Naquel City'
        )->setComment(
            'Courier Management Table'
        );
        $setup->getConnection()->createTable($table);

        $tableDistrict = $setup->getConnection()->newTable(
            $setup->getTable('courier_manager_districts')
        )->addColumn(
            'district_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'District ID'
        )->addColumn(
            'en_district_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'District Name'
        )->addColumn(
            'ar_district_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'District Name'
        )->addColumn(
            'naquel_city',
            Table::TYPE_TEXT,
            255,
            ['nulluable' => false],
            'City Value'
        )->addColumn(
            'naquel_dist_code',
            Table::TYPE_TEXT,
            255,
            ['nulluable' => false],
            'Naquel Dist Code'
        )->setComment(
            'District Management Table'
        );
        $setup->getConnection()->createTable($tableDistrict);

        $setup->endSetup();
    }
}
