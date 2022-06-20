<?php

namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class MassDelete extends Zip
{
    /**
     * @return void
     */
    public function execute()
    {

        // Get IDs of the selected zip
        $zipIds = $this->getRequest()->getParam('Zip_Name');
        $zipIds = explode(',',$zipIds);
        if(count($zipIds) > 0){
            foreach ($zipIds as $zipId) {
                try {
                    /** @var $zipModel \Custom\City\Model\zip */
                    $zipModel = $this->_zipFactory->create();
                    $zipModel->load($zipId)->delete();
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }
            }
        }else{
            $this->messageManager->addError(
                __('Please select option first.')
            );
        }

        if (count($zipIds)) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', count($zipIds))
            );
        }

        $this->_redirect('*/*/index');
    }
}