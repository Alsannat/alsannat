<?php

namespace Ced\City\Block\Adminhtml\System\Config;


class Import extends \Magento\Framework\Data\Form\Element\AbstractElement
{

    protected function _construct()
    {
        parent::_construct();
        $this->setType('file');
    }


    public function getElementHtml()
    {
        $html = '';
        $html .= parent::getElementHtml();
        return $html;
    }
}
