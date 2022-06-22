<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Setup;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var \Wyomind\ElasticsearchCore\Setup\ProductSetupFactory 
     */
    protected $_productSetupFactory;

    /**
     * @param \Wyomind\ElasticsearchCore\Setup\ProductSetupFactory $productSetupFactory
     */
    public function __construct(ProductSetupFactory $productSetupFactory)
    {
        $this->_productSetupFactory = $productSetupFactory;
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        unset($context);

        $productSetup = $this->_productSetupFactory->create(['setup' => $setup]);
        $productSetup->installEntities();
        $installer = $setup;
        $installer->startSetup();
        $installer->endSetup();
    }
}