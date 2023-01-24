<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Model\System\Config\Source;

class ProductIdentifier
{
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * @return type
     */
    public function toOptionArray()
    {
        $typeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;
        $attributeList = $this->_attributeRepository->getList($typeCode, $this->_searchCriteria)->getItems();
        $tmp = [];
        $tmp[] = ['label' => '-- not set --', 'value' => ''];
        $tmp[] = ['label' => 'ID', 'value' => 'entity_id'];
        foreach ($attributeList as $attribute) {
            if ($attribute->getIsUnique()) {
                $tmp[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getDefaultFrontendLabel()];
            }
        }
        return $tmp;
    }
}