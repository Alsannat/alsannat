<?php

namespace Amasty\Acart\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Operation\UpgradeTo101
     */
    private $upgradeTo101;

    /**
     * @var Operation\UpgradeTo108
     */
    private $upgradeTo108;

    /**
     * @var Operation\UpgradeTo110
     */
    private $upgradeTo110;

    /**
     * @var Operation\UpgradeTo180
     */
    private $upgradeTo180;

    /**
     * @var Operation\UpgradeTo190
     */
    private $upgradeTo190;

    /**
     * @var Operation\UpgradeTo191
     */
    private $upgradeTo191;

    /**
     * @var Operation\UpgradeTo1120
     */
    private $upgradeTo1120;

    /**
     * @var Operation\UpgradeTo1121
     */
    private $upgradeTo1121;

    /**
     * @var Operation\CreateEmailTemplateTable
     */
    private $createEmailTemplateTable;

    public function __construct(
        Operation\UpgradeTo101 $upgradeTo101,
        Operation\UpgradeTo108 $upgradeTo108,
        Operation\UpgradeTo110 $upgradeTo110,
        Operation\UpgradeTo180 $upgradeTo180,
        Operation\UpgradeTo190 $upgradeTo190,
        Operation\UpgradeTo191 $upgradeTo191,
        Operation\UpgradeTo1120 $upgradeTo1120,
        Operation\UpgradeTo1121 $upgradeTo1121,
        Operation\CreateEmailTemplateTable $createEmailTemplateTable
    ) {
        $this->upgradeTo101 = $upgradeTo101;
        $this->upgradeTo108 = $upgradeTo108;
        $this->upgradeTo110 = $upgradeTo110;
        $this->upgradeTo180 = $upgradeTo180;
        $this->upgradeTo190 = $upgradeTo190;
        $this->upgradeTo191 = $upgradeTo191;
        $this->upgradeTo1120 = $upgradeTo1120;
        $this->upgradeTo1121 = $upgradeTo1121;
        $this->createEmailTemplateTable = $createEmailTemplateTable;
        $this->upgradeTo1121 = $upgradeTo1121;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $this->upgradeTo101->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.0.8', '<')) {
            $this->upgradeTo108->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->upgradeTo110->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->upgradeTo180->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.9.0', '<')) {
            $this->upgradeTo190->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.9.1', '<')) {
            $this->upgradeTo191->execute($setup);
        }

        if (!$context->getVersion() || version_compare($context->getVersion(), '1.11.1', '<')) {
            $this->createEmailTemplateTable->execute($setup);
            $this->upgradeTo1120->execute($setup);
        }

        if (version_compare($context->getVersion(), '1.12.1', '<')) {
            $this->upgradeTo1121->execute($setup);
        }

        $setup->endSetup();
    }
}
