<?php
/**
 * Adminhtml contest list block
 *
 */
namespace Custom\Contest\Block\Adminhtml;

class Contest extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_contest';
        $this->_blockGroup = 'Custom_Contest';
        $this->_headerText = __('Contest');
         parent::_construct();
        //$this->_addButtonLabel = __('Add New Contest');
       $this->buttonList->remove('add');
        /*if ($this->_isAllowedAction('Custom_Contest::save')) {
            $this->buttonList->update('add', 'label', __('Add New Contest'));
        } else {
            $this->buttonList->remove('add');
        }*/
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
