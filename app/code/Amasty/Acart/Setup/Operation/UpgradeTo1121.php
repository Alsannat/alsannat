<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\ResourceModel\Schedule as ScheduleResource;
use Amasty\Acart\Model\Schedule as ScheduleModel;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeTo1121
{
    /**
     * @var CreateGuestCustomerQuotesTable
     */
    private $createGuestCustomerQuotesTable;

    public function __construct(
        CreateGuestCustomerQuotesTable $createGuestCustomerQuotesTable
    ) {
        $this->createGuestCustomerQuotesTable = $createGuestCustomerQuotesTable;
    }

    public function execute(SchemaSetupInterface $setup)
    {
        $this->createGuestCustomerQuotesTable->execute($setup);

        $connection = $setup->getConnection();

        $scheduleTable = $setup->getTable(ScheduleResource::TABLE_NAME);
        $connection->addColumn(
            $scheduleTable,
            ScheduleModel::USE_CAMPAIGN_UTM,
            [
                'type' => Table::TYPE_BOOLEAN,
                'nullable' => false,
                'default' => true,
                'comment' => 'Use Schedule Google Analytics UTM Parameters'
            ]
        );
        $connection->addColumn(
            $scheduleTable,
            ScheduleModel::UTM_SOURCE,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Utm Source'
            ]
        );
        $connection->addColumn(
            $scheduleTable,
            ScheduleModel::UTM_MEDIUM,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Utm Medium'
            ]
        );
        $connection->addColumn(
            $scheduleTable,
            ScheduleModel::UTM_CAMPAIGN,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => null,
                'comment' => 'Utm Campaign'
            ]
        );
    }
}
