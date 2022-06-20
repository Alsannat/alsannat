<?php
namespace Evincemage\CreateAttribute\Setup;

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
            'image1',
            [   
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'Image1',
                'input' => 'image',
                'backend' => 'Evincemage\ProductAttributes\Model\Product\Attribute\Backend\Image',
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
            'image2',
            [   
                'group' => 'General',
                'type' => 'varchar',
                'label' => 'Image2',
                'input' => 'image',
                'backend' => 'Evincemage\ProductAttributes\Model\Product\Attribute\Backend\Image',
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
            \Magento\Catalog\Model\Product::ENTITY, 'image1_text',[
        'group' => 'General',
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'Image1 Text',
        'input' => 'textarea',
        'class' => '',
        'source' => '',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
        'wysiwyg_enabled' => true,
        'is_html_allowed_on_front' => true,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,
        'used_in_product_listing' => true,
        'wysiwyg_enabled' => true,
        'unique' => false,
        'apply_to' => ''
            ]
        )->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'image2_text',[
        'group' => 'General',
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'Image2 Text',
        'input' => 'textarea',
        'class' => '',
        'source' => '',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
        'wysiwyg_enabled' => true,
        'is_html_allowed_on_front' => true,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,
        'used_in_product_listing' => true,
        'wysiwyg_enabled' => true,
        'unique' => false,
        'apply_to' => ''
            ]
        )->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'warranty_tab',[
        'group' => 'General',
        'type' => 'text',
        'backend' => '',
        'frontend' => '',
        'label' => 'Warranty Tab',
        'input' => 'textarea',
        'class' => '',
        'source' => '',
        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
        'wysiwyg_enabled' => true,
        'is_html_allowed_on_front' => true,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'searchable' => false,
        'filterable' => false,
        'comparable' => false,
        'visible_on_front' => true,
        'used_in_product_listing' => true,
        'wysiwyg_enabled' => true,
        'unique' => false,
        'apply_to' => ''
            ]
        );
    }
}