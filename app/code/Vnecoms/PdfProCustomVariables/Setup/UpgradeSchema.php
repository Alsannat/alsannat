<?php

namespace Vnecoms\PdfProCustomVariables\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(\Magento\Framework\Setup\SchemaSetupInterface $setup, \Magento\Framework\Setup\ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();


        if (version_compare($context->getVersion(), '2.0.0.1') < 0) {
            // $table = $setup->getConnection()->addColumn($setup->getTable('ves_pdfprocustomvariables_customvariables'), 'attribute_id_customer', 'int unsigned NOT NULL AFTER attribute_id');

            $connection = $installer->getConnection();

            $installer->getConnection()->addColumn(
                $installer->getTable('ves_pdfprocustomvariables_customvariables'),
                'attribute_id_customer',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => false,
                    'unsigned' => true,
                    'comment' => 'Attribute ID Customer',
                ]
            );

            /*$table = $setup->getConnection()->newTable(
                $setup->getTable('ves_pdfprocustomvariables_customvariables')
            )->addColumn(
                'attribute_id_customer',
                Table::TYPE_INTEGER,
                11,
                ['unsigned' => true, 'nullable' => false],
                'Attribute Id Customer'
            );
            $setup->getConnection()->createTable($table);*/
            /*
             * End create table vnecoms_vendor_cms_app
             */
        }

        $setup->endSetup();
    }
}
