<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;

/**
 * Config field for the selection of the product attributes filterable in the layered navigation
 */
class Attributes extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * Columns of the grid
     * @var array
     */
    protected $_columns = [];

    /**
     * Renderer of the product attributes dropdown
     * @var \Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\FilterableAttributes
     */
    private $_filterableAttributesRenderer;

    /**
     * Rendered of the yes/no dropdown to display or not the number of products for each attribute values
     * @var \Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\ShowResultsCount
     */
    private $_showResultsCountRenderer;

    /**
     * Drag and drop renderer
     * @var \Magento\Framework\View\Element\Html\Link
     */
    private $_handleRenderer;

    /**
     * @var bool
     */
    protected $_addAfter = true;

    /**
     * @var string
     */
    protected $_addButtonLabel = '';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_addButtonLabel = __('Add an attribute');
    }

    /**
     * Get the filterable attribute renderer
     * @return \Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\FilterableAttributes|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getFilterableAttributesRenderer()
    {
        if (!$this->_filterableAttributesRenderer) {
            $this->_filterableAttributesRenderer = $this->getLayout()->createBlock(
                '\Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\FilterableAttributes', '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_filterableAttributesRenderer;
    }

    /**
     * Get the "show results count" renderer
     * @return \Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\ShowResultsCount|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getShowResultsCountRenderer()
    {
        if (!$this->_showResultsCountRenderer) {
            $this->_showResultsCountRenderer = $this->getLayout()->createBlock(
                '\Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer\ShowResultsCount', '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_showResultsCountRenderer;
    }

    /**
     * Get the drag and drop renderer
     * @return \Magento\Framework\View\Element\BlockInterface|\Magento\Framework\View\Element\Html\Link
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getHandleRenderer()
    {
        if (!$this->_handleRenderer) {
            $this->_handleRenderer = $this->getLayout()->createBlock(
                '\Magento\Framework\View\Element\Html\Link', '', ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->_handleRenderer;
    }

    /**
     * @{inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'handle', [
                'label' => '',
                'renderer' => $this->getHandleRenderer()
            ]
        );
        $this->addColumn(
            'attribute_code', [
                'label' => __('Filterable Attribute'),
                'renderer' => $this->getFilterableAttributesRenderer()
            ]
        );
        $this->addColumn(
            'show_results_count', [
                'label' => __('Show results count'),
                'renderer' => $this->getShowResultsCountRenderer()
            ]
        );
        $this->addColumn(
            'position', [
                'label' => __('Position')
            ]
        );
        $this->_addAfter = true;
    }

    /**
     * @param \Magento\Framework\DataObject $row
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $attributeCode = $row->getAttributeCode();
        $options = [];
        if ($attributeCode) {
            $options['option_' . $this->getFilterableAttributesRenderer()->calcOptionHash($attributeCode)] = 'selected="selected"';
        }
        $showResultsCount = $row->getShowResultsCount();
        $options['option_' . $this->getShowResultsCountRenderer()->calcOptionHash($showResultsCount)] = 'selected="selected"';
        $row->setData('option_extra_attrs', $options);
    }

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'position') {
            $this->_columns[$columnName]['class'] = 'input-text required-entry validate-number';
            $this->_columns[$columnName]['style'] = 'width:50px; display:none';
        }
        return parent::renderCellTemplate($columnName);
    }
}