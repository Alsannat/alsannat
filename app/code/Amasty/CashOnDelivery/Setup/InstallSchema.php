<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Setup;

use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codingStandardsIgnoreStart
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $installer, ModuleContextInterface $context)
    {
        $installer->startSetup();

        /**
         * Create table 'amasty_cash_on_delivery_fee_quote'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable(PaymentFee::TABLE_NAME))
            ->addColumn(
                PaymentFeeInterface::ENTITY_ID,
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                PaymentFeeInterface::QUOTE_ID,
                Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => true],
                'Quote Id'
            )
            ->addColumn(
                PaymentFeeInterface::AMOUNT,
                Table::TYPE_DECIMAL,
                null,
                ['nullable' => false, 'precision' => '12', 'scale' => '2'],
                'Amount'
            )
            ->addColumn(
                PaymentFeeInterface::BASE_AMOUNT,
                Table::TYPE_DECIMAL,
                null,
                ['nullable' => false, 'precision' => '12', 'scale' => '2'],
                'Base Amount'
            )
            ->addForeignKey(
                $installer->getFkName(
                    PaymentFee::TABLE_NAME,
                    PaymentFeeInterface::QUOTE_ID,
                    'quote',
                    'entity_id'
                ),
                PaymentFeeInterface::QUOTE_ID,
                $installer->getTable('quote'),
                'entity_id',
                Table::ACTION_SET_NULL
            )
            ->setComment('Amasty Cash on Delivery Fee Table');

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
