<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\Renderer;

/**
 * Yes/No dropdown for choosing if the number of products should be displayed in the layered navigation
 */
class ShowResultsCount extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * Available options of the dropdown
     * @var array
     */
    protected $_optionsValues = [];

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->addOption(1, __('Yes'));
            $this->addOption(0, __('No'));
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
}