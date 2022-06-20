<?php

namespace Webengage\Event\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

use Webengage\Event\Setup\SchemaHelper;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $shelper = new SchemaHelper();
        $shelper->createTableIfNotExists($installer);
        $shelper->createAllConfigsIfNotExists($installer);

        $installer->endSetup();
    }
}
