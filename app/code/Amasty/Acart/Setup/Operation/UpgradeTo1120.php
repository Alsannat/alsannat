<?php
declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\RuleQuote as RuleQuoteResource;
use Amasty\Acart\Model\ResourceModel\Schedule as ScheduleResource;
use Amasty\Acart\Model\RuleQuote as RuleQuoteModel;
use Amasty\Acart\Model\Schedule as ScheduleModel;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeTo1120
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();

        $tableRuleQuote = $setup->getTable(RuleQuoteResource::MAIN_TABLE);
        $connection->addColumn(
            $tableRuleQuote,
            RuleQuoteModel::CUSTOMER_PHONE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'length' => 50,
                'comment' => 'Phone Number'
            ]
        );

        $scheduleTable = $setup->getTable(ScheduleResource::TABLE_NAME);
        $connection->modifyColumn(
            $scheduleTable,
            ScheduleModel::TEMPLATE_ID,
            [
                'type' => Table::TYPE_INTEGER,
                'unsigned' => true,
                'nullable' => true,
                'default' => null,
                'comment' => 'Template ID'
            ]
        );
    }
}
