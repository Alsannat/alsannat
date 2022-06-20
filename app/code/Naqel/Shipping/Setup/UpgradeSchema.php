<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Change column name order_id to entity_id in naqel_shipping_waybill_record table
        */
        $installer = $setup; 
        $installer->startSetup();
        $tableName = $setup->getTable('naqel_shipping_naqel_city');
        $tableNameWaybill = $setup->getTable('naqel_shipping_waybill_record');
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
              if ($setup->getConnection()->isTableExists($tableName) == true) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('naqel_shipping_naqel_city'),
                    'client_country_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'client country name'
                    ]
                );
                $installer->getConnection()->addColumn(
                    $installer->getTable('naqel_shipping_naqel_city'),
                    'client_city_name',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'client city name'
                    ]
                );
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('naqel_shipping_naqel_city'),
                    'client_city_name_ar',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'client city name arabic'
                    ]
                );
            }
            if ($setup->getConnection()->isTableExists($tableNameWaybill) == true) {
                $installer->getConnection()->addColumn(
                    $installer->getTable('naqel_shipping_waybill_record'),
                    'asr_waybill_no',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'ASR Waybill No'
                    ]
                );
            }
            $installer->endSetup();
        }
    }        
}
