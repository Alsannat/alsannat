<?php
namespace MGS\Popup\Controller\Adminhtml\PopupGrid;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		$id = $this->getRequest()->getParam('popup_id');
		try {
				$popup = $this->_objectManager->create('MGS\Popup\Model\Popup')->load($id);
				$popup->delete();
                $this->messageManager->addSuccess(
                    __('Delete successfully !')
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
	    $this->_redirect('*/*/');
    }
}
