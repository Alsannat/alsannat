<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;

/**
 * Class Servers
 */
class Servers extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_urlBuilder = $context->getUrlBuilder();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value">';
            $html .= $this->_getElementHtml($element);
        }
        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }
        $html .= '<button '
            . 'id="es_test_servers" '
            . "callback_url=" . $this->_urlBuilder->getUrl(\Wyomind\ElasticsearchCore\Helper\Url::TEST_CALLBACK_URL) . ' '
            . 'onClick="return false;"'
            . 'style="inline-block" '
            . 'class="action-default scalable save primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"'
            . '><span><span>' . __('Check servers') . '</span></span></button>';
        $html .= '</td>';

        return $html;
    }
}