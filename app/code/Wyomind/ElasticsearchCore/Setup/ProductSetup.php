<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Setup;

/**
 * Attribute installation
 */
class ProductSetup extends \Magento\Eav\Setup\EavSetup
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory = null;

    /**
     * ProductSetup constructor.
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Eav\Model\Entity\Setup\Context $context
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Eav\Model\Entity\Setup\Context $context,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory $attrGroupCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
    
        $this->_productFactory = $productFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * @return array
     */
    public function getDefaultEntities()
    {
        $attributes = [];
        
        $attributes['product_weight'] = [
            'group' => 'Wyomind Elasticsearch Core',
            'label' => 'Product weight',
            'default' => '1',
            'note' => '',
            'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'searchable' => true,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'visible_in_advanced_search' => false,
            'unique' => false,
            'frontend_class' => '',
            'used_in_product_listing' => 1,
            'input' => 'select',
            'type' => 'int',
            'source' => 'Wyomind\ElasticsearchCore\Model\Config\Source\ProductWeight',
            'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend'
        ];

        return [
            'catalog_product' => [
                'entity_model' => 'Magento\Catalog\Model\ResourceModel\Product',
                'attribute_model' => 'Magento\Catalog\Model\ResourceModel\Eav\Attribute',
                'table' => 'catalog_product_entity',
                'additional_attribute_table' => 'catalog_eav_attribute',
                'entity_attribute_collection' => 'Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection',
                'attributes' => $attributes
            ]
        ];
    }
}