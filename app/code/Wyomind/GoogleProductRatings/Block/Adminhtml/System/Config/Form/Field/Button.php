<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Block\Adminhtml\System\Config\Form\Field;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Wyomind_GoogleProductRatings::system/config/button.phtml';
    
    /**
     * Return HTML element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
    
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        
        return parent::render($element);
    }
    
    /**
     * Generate HTML button
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $website = $this->_request->getParam('website');
        $store = $this->_request->getParam('store');
        
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')->setData([
            'id' => 'googleproductratings_button',
            'label' => __('Generate now!'),
        ])->setDataAttribute([
            'url' => $this->getUrl(
                'googleproductratings/feed/generate',
                ['website' => $website, 'store' => $store]
            )
        ]);

        return $button->toHtml();
    }
}
