<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 01/07/2017
 * Time: 15:31
 */

namespace Magenest\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            $installer = $setup;
            $installer->startSetup();
            $updatedTable = [];
            $updatedTable[] = $installer->getTable('quote_item');
            $updatedTable[] = $installer->getTable('sales_order_item');
            $updatedTable[] = $installer->getTable('sales_invoice_item');
            $updatedTable[] = $installer->getTable('sales_creditmemo_item');

            $columns = [
                'base_applied_giftcard_amount' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Base Applied Gift Card Amount'
                ],
                'applied_giftcard_amount' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    'length' => '12,4',
                    'nullable' => false,
                    'default' => '0.0000',
                    'comment' => 'Applied Gift Card Amount'
                ],
            ];

            $connection = $installer->getConnection();
            foreach ($updatedTable as $table) {
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($table, $name, $definition);
                }
            }
            $installer->endSetup();
        }

        //add the draft table of the template
        if (version_compare($context->getVersion(), '2.0.2', '<')) {
            $installer = $setup;
            $installer->startSetup();

            $table =  $installer->getTable('magenest_giftcard_template');
            $connection = $installer->getConnection();
            $name = 'draft';
            $definition = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Template Mode'
            ];

            $connection->addColumn($table, $name, $definition);

            $installer->endSetup();
        }
        //add the table to save the selected main image and selected background image
        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            $installer = $setup;
            $installer->startSetup();

            $table =  $installer->getTable('magenest_giftcard_template');
            $connection = $installer->getConnection();

            $name = 'saved_background_selected';
            $definition = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Background image'
            ];

            $connection->addColumn($table, $name, $definition);

            $name = 'saved_image_selected';
            $definition = [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => true,
                'comment'  => 'Main image'
            ];

            $connection->addColumn($table, $name, $definition);

            $installer->endSetup();
        }
     }
}