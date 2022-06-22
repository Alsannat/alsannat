<?php
namespace MGS\Popup\Block\Adminhtml;
class Popup extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
		
        $this->_controller = 'adminhtml_popup';/*block grid.php directory*/
        $this->_blockGroup = 'MGS_Popup';
        $this->_headerText = __('Popup');
        $this->_addButtonLabel = __('Add New Popup'); 
        parent::_construct();
		
    }
}
