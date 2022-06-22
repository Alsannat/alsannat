<?php
/**
 * Copyright © 2015 MGS. All rights reserved.
 */

namespace MGS\Popup\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
	
        $installer = $setup;

        $installer->startSetup();

		/**
         * Create table 'mgs_popup'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('mgs_popup')
        )
		->addColumn(
            'popup_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'mgs_popup'
        )->addColumn(
            'popup_scroll',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Popup Scroll'
        )->addColumn(
            'popup_width',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 800],
            'mgs_popup'
        )->addColumn(
            'popup_height',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 450],
            'mgs_popup'
        )->addColumn(
            'padding_top',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Padding Top'
        )->addColumn(
            'padding_bottom',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => 0],
            'Padding Bottom'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Title'
        )->addColumn(
            'background_color',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Background Color'
        )->addColumn(
            'background_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Background Image'
        )->addColumn(
            'css_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Custom Class'
        )->addColumn(
            'content_html',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Content Html'
        )->addColumn(
            'enable_on',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Enable On'
        )->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Customer Group'
        )->addColumn(
            'time_start',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'Start Date'
        )->addColumn(
            'time_end',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => true],
            'End Date'
        )->addColumn(
            'enb_cms',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Show countdown time'
        )->addColumn(
            'check_closed',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Show Checkbox'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 1],
            'Status'
        )->addColumn(
            'background_repeat',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Background Repeat'
        )->addColumn(
            'background_cover',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => 0],
            'Background Cover'
        )->addColumn(
            'background_position_x',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Background Position X'
        )->addColumn(
            'background_position_y',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            20,
            [],
            'Background Position Y'
        );
		
		$installer->getConnection()->createTable($table);
		
		/**
         * Create table 'mgs_popup_store'
         */
		$table = $installer->getConnection()
			->newTable($installer->getTable('mgs_popup_store'))
			->addColumn(
					'popup_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Popup Id'
			)
			->addColumn(
					'store_id', \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, null, ['unsigned' => true, 'nullable' => false, 'primary' => true], 'Store Id'
			)
			->addIndex(
					$setup->getIdxName('mgs_popup_store', ['store_id']), ['store_id']
			)->addForeignKey(
				$installer->getFkName('mgs_popup_store', 'popup_id', 'mgs_popup', 'popup_id'),
				'popup_id',
				$installer->getTable('mgs_popup'),
				'popup_id',
				\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
			)->addForeignKey(
				$installer->getFkName('mgs_popup_store', 'store_id', 'store', 'store_id'),
				'store_id',
				$installer->getTable('store'),
				'store_id',
				\Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
			)->setComment('Popup of Store');
			
		$installer->getConnection()->createTable($table);
        $installer->endSetup();

    }
}
