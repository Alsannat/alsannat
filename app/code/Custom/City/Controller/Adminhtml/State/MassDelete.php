<?php

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class MassDelete extends State
{
    /**
     * @return void
     */
    public function execute()
    {
        // Get IDs of the selected State
        $stateIds = $this->getRequest()->getParam('state');

        foreach ($stateIds as $stateId) {
            try {
                /** @var $stateModel \Custom\City\Model\State */
                $stateModel = $this->_stateFactory->create();
                $stateModel->load($stateId)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        if (count($stateIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($stateIds))
            );
        }

        $this->_redirect('*/*/index');
    }
}