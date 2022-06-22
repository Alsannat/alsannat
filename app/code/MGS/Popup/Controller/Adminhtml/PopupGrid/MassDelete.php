<?php
namespace MGS\Popup\Controller\Adminhtml\PopupGrid;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		
		 $ids = $this->getRequest()->getParam('popup_id');
		if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select popup(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->create('MGS\Popup\Model\Popup')->load($id);
					$row->delete();
				}
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
		 $this->_redirect('*/*/');
    }
}
