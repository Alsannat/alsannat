<?php

namespace Custom\City\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class City extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_city';
        $this->_blockGroup = 'Custom_City';
        $this->_headerText = __('Manage Cities');
        $this->_addButtonLabel = __('Add city');
        parent::_construct();
    }
}
 