<?php

namespace Custom\City\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Zip extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_zip';
        $this->_blockGroup = 'Custom_City';
        $this->_headerText = __('Manage Neighborhoods Codes');
        $this->_addButtonLabel = __('Add Neighborhoods code');
        parent::_construct();
    }
}
 