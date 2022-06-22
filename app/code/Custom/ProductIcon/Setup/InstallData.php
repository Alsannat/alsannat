<?php
namespace Custom\ProductIcon\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Catalog\Model\ResourceModel\Product as ResourceProduct;

class InstallData implements InstallDataInterface
{
    protected $_attributeSet;
    protected $_eavSetupFactory;
    protected $_resourceProduct;
 
    public function __construct(
        AttributeSet $attributeSet,
        EavSetupFactory $eavSetupFactory,
        ResourceProduct $resourceProduct
    ) {
        $this->_attributeSet    = $attributeSet;
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->_resourceProduct = $resourceProduct;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        $eavSetup = $this->_eavSetupFactory->create(["setup"=>$setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'static_desktop',
            [   
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'Static image for desktop',
                'input' => 'image',
                'backend' => 'Custom\ProductIcon\Model\Product\Attribute\Backend\File',
                'frontend' => '',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => '', // applicable for simple and configurable product 
                'used_in_product_listing' => false
            ]
        )->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'static_mobile',
            [   
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'static image for mobile',
                'input' => 'image',
                'backend' => 'Custom\ProductIcon\Model\Product\Attribute\Backend\File',
                'frontend' => '',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'apply_to' => '', // applicable for simple and configurable product 
                'used_in_product_listing' => false
            ]
        );

        // // assign attribute to attribute set
        // $entityType = $this->_resourceProduct->getEntityType();
        // $attributeSetCollection = $this->_attributeSet->setEntityTypeFilter($entityType);
        // foreach ($attributeSetCollection as $attributeSet) {
        //     $eavSetup->addAttributeToSet("catalog_product", $attributeSet->getAttributeSetName(), "General", "agreement_file");
        // }
    }
}