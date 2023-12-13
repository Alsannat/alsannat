<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Model\System\Config\Source;

class ProductAttribute
{
    /**
     * Attributes codes which are disabled for export
     * @var array
     */
    private $_disabledAttributes = ['old_id', 'tier_price', 'category_ids', 'has_options', 'is_returnable', 'required_options', 'quantity_and_stock_status'];
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @return array
     */
    public function toOptionArray()
    {
        // Exclude not exportable attributes (e.g afterLoad method returns an array in Magento\Catalog\Model\Product\Attribute\Backend\Stock)
        $this->_searchCriteriaBuilder->addFilter('attribute_code', $this->_disabledAttributes, 'nin')->addFilter('frontend_label', '', 'notnull');
        $searchCriteria = $this->_searchCriteriaBuilder->create();
        $typeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;
        $attributeList = $this->_attributeRepository->getList($typeCode, $searchCriteria)->getItems();
        $tmp = [];
        $tmp[] = ['label' => '-- not set --', 'value' => ''];
        $tmp[] = ['label' => 'ID', 'value' => 'entity_id'];
        foreach ($attributeList as $attribute) {
            $tmp[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getDefaultFrontendLabel()];
        }
        return $tmp;
    }
}