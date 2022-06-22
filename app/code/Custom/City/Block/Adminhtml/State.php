<?php

namespace Custom\City\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class State extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_state';
        $this->_blockGroup = 'Custom_City';
        $this->_headerText = __('Manage States');
        $this->_addButtonLabel = __('Add state');
        parent::_construct();
    }
}
 