<?php

namespace Amasty\Acart\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table as DdlTable;

class UpgradeTo110
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('amasty_acart_schedule');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'send_same_coupon',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Send Same Coupon'
            ]
        );
    }
}
