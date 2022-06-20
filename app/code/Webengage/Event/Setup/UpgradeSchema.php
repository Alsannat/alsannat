<?php

namespace Webengage\Event\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;

use Webengage\Event\Setup\SchemaHelper;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        // Idempotent. Since we don't use complex migrations yet, we don't need to do
        // complicated version checks.
        $shelper = new SchemaHelper();
        $shelper->createTableIfNotExists($installer);
        $shelper->createAllConfigsIfNotExists($installer);
     
        $installer->endSetup();
    }
}
