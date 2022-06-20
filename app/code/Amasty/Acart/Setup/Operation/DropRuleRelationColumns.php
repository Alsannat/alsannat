<?php

declare(strict_types=1);

namespace Amasty\Acart\Setup\Operation;

use Magento\Framework\Setup\SchemaSetupInterface;

class DropRuleRelationColumns
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function execute(SchemaSetupInterface $setup)
    {
        $ruleTable = $setup->getTable('amasty_acart_rule');
        $setup->getConnection()->dropColumn($ruleTable, 'customer_group_ids');
        $setup->getConnection()->dropColumn($ruleTable, 'store_ids');
    }
}
