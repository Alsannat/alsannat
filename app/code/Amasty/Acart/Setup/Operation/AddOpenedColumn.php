<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\History;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddOpenedColumn
{
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(History::TABLE_NAME);

        $setup->getConnection()->addColumn(
            $table,
            \Amasty\Acart\Model\History::OPENED_COUNT,
            [
                'type' => DdlTable::TYPE_INTEGER,
                'nullable' => false,
                'default' => 0,
                'comment' => 'Email open counter'
            ]
        );
    }
}
