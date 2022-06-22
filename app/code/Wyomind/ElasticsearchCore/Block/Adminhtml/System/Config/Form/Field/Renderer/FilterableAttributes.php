<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer;

/**
 * Dropdown list of filterable product attributes
 */
class FilterableAttributes extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeListFactory
     */
    protected $_filterableAttributeListFactory = null;

    /**
     * List of options for the dropdown
     * @var array
     */
    protected $_optionsValues = [];

    /**
     * FilterableAttributes constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Catalog\Model\Layer\Category\FilterableAttributeListFactory $filterableAttributeListFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeListFactory $filterableAttributeListFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_filterableAttributeListFactory = $filterableAttributeListFactory;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $filterableAttributes = $this->getFilterableAttributes();
            foreach ($filterableAttributes as $option) {
                $this->addOption($option['value'], $option['label']);
            }
        }
        
        return parent::_toHtml();
    }

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Get the options for the dropdown
     * @return array the list of options
     */
    private function getFilterableAttributes()
    {
        if ($this->_optionsValues == null) {
            $list = $this->_filterableAttributeListFactory->create()->getList();
            foreach ($list as $attribute) {
                if ($attribute->getAttributeCode() == 'price') {
                    $this->_optionsValues[] = ['value' => 'final_price', 'label' => $attribute->getFrontendLabel()];
                } else {
                    $this->_optionsValues[] = ['value' => $attribute->getAttributeCode(), 'label' => $attribute->getFrontendLabel()];
                }
            }
            $this->_optionsValues[] = ['value' => 'rating', 'label' => __('Ratings')];
            $this->_optionsValues[] = ['value' => 'categories', 'label' => __('Categories')];
            $this->_optionsValues[] = ['value' => 'quantity_and_stock_status_ids', 'label' => __('Stock Status')];
            usort($this->_optionsValues, function ($a, $b) {
                return mb_strtolower($a['label']) > mb_strtolower($b['label']);
            });
        }
        
        return $this->_optionsValues;
    }
}