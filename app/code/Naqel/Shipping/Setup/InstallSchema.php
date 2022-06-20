<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Setup;

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
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();


        /**
         * Creating table naqel_shipping_naqel_city
         */
		if (!$installer->tableExists('naqel_shipping_waybill_record')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('naqel_shipping_waybill_record')
            )
                ->addColumn(
                        'waybill_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        null,
                        [
                            'identity' => true,
                            'nullable' => false,
                            'primary'  => true,
                            'unsigned' => true,
                        ],
                        'waybill_id'
                    )
                    ->addColumn(
                        'entity_id',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable => false'],
                        'entity_id'
                    )
                    ->addColumn(
                        'has_error',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        ['nullable => false'],
                        'has_error'
                    )
                    ->addColumn(
                        'waybill_no',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'waybill_no'
                    )
                    
                    ->addColumn(
                        'booking_ref_no',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'booking_ref_no'
                    )
                    ->addColumn(
                        'waybill_key',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        100,
                        [],
                        'waybill_key'
                    )
                    ->addColumn(
                        'message',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        255,
                        [],
                        'message'
                    )
                    ->addColumn(
                        'created_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                        'Created At'
                    )->addColumn(
                        'updated_at',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                        'Updated At')
                    ->setComment('Waybill');
            $installer->getConnection()->createTable($table);

            
        }

		/**
		 * Creating table naqel_shipping_naqel_city
		 */
		if (!$installer->tableExists('naqel_shipping_naqel_city')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('naqel_shipping_naqel_city')
            )->addColumn(
			'naqel_city_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'code',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'code'
		)->addColumn(
			'city_name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'city_name'
		)
		->addColumn(
			'country_code',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'country_code'
		)
		->addColumn(
			'oda',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'oda'
		)
        ->addColumn(
            'city_longitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Longitude of the city'
        )
        ->addColumn(
            'city_lattitude',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Lattitude of the city'
        )->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			1,
			['nullable' => false,'default' => 0],
			'Status'
		)->addColumn(
			'created_at',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false],
			'Created At'
		)->setComment(
            'Naqel Shipping Table'
        );
		$installer->getConnection()->createTable($table);
	}	
		$installer->endSetup();
	}
}