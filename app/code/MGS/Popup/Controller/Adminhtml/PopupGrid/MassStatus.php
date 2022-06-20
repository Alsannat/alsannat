<?php
namespace MGS\Popup\Controller\Adminhtml\PopupGrid;

class MassStatus extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
		 $ids = $this->getRequest()->getParam('popup_id');
		 $status = $this->getRequest()->getParam('status');
		if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select popup(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->create('MGS\Popup\Model\Popup')->load($id);
					$row->setData('status',$status)
							->save();
				}
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been saved.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
		
		$this->_redirect('*/*/');
    }
}
