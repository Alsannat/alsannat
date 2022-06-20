<?php

namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class Delete extends Zip
{
    /**
     * @return void
     */
    public function execute()
    {
        $zipId = (int) $this->getRequest()->getParam('id');

        if ($zipId) {
            /** @var $zipModel \Custom\City\Model\zip */
            $zipModel = $this->_zipFactory->create();
            $zipModel->load($zipId);

            // Check this zip exists or not
            if (!$zipModel->getId()) {
                $this->messageManager->addError(__('This zip code no longer exists.'));
            } else {
                try {
                    // Delete zip
                    $zipModel->delete();
                    $this->messageManager->addSuccess(__('The zip code has been deleted.'));

                    // Redirect to grid page
                    $this->_redirect('*/*/');
                    return;
                } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                    $this->_redirect('*/*/edit', ['id' => $zipModel->getId()]);
                }
            }
        }
    }
}