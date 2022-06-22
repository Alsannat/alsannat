<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Amasty\Acart\Model\EmailTemplate as EmailTemplateModel;
use Amasty\Acart\Model\ResourceModel\EmailTemplate as EmailTemplateResource;
use Amasty\Acart\Model\ResourceModel\Schedule as ScheduleResource;
use Amasty\Acart\Model\Schedule as ScheduleModel;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class CreateEmailTemplateTable
{
    public function execute(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->createTable(
            $this->createTable($setup)
        );
    }

    private function createTable(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(EmailTemplateResource::TABLE_NAME);
        $scheduleTable = $setup->getTable(ScheduleResource::TABLE_NAME);

        return $setup->getConnection()
            ->newTable(
                $table
            )->setComment(
                'Amasty Acart Email Template Table'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Template Id'
            )->addColumn(
                EmailTemplateModel::SCHEDULE_ID,
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Schedule Id'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_CODE,
                Table::TYPE_TEXT,
                150,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Template Name'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_TEXT,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => false,
                    'default' => ''
                ],
                'Template Content'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_STYLES,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Template Styles'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_TYPE,
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                    'default' => null
                ],
                'Template Type'
            )->addColumn(
                EmailTemplateModel::TEMPLATE_SUBJECT,
                Table::TYPE_TEXT,
                200,
                [
                    'nullable' => false
                ],
                'Template Subject'
            )->addColumn(
                EmailTemplateModel::ORIG_TEMPLATE_VARIABLES,
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true,
                    'default' => null
                ],
                'Original Template Variables'
            )->addColumn(
                EmailTemplateModel::IS_LEGACY,
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => 0
                ],
                'Is Legacy'
            )->addForeignKey(
                $setup->getFkName(
                    $table,
                    EmailTemplateModel::SCHEDULE_ID,
                    $scheduleTable,
                    ScheduleModel::SCHEDULE_ID
                ),
                EmailTemplateModel::SCHEDULE_ID,
                $scheduleTable,
                ScheduleModel::SCHEDULE_ID,
                Table::ACTION_CASCADE
            )->addIndex(
                $setup->getIdxName(
                    $table,
                    [EmailTemplateModel::TEMPLATE_CODE],
                    AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                [EmailTemplateModel::TEMPLATE_CODE],
                ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
            );
    }
}
