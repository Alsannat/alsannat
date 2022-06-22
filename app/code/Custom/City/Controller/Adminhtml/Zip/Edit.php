<?php

namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class Edit extends Zip
{

    /**
     * @return void
     */
    public function execute()
    {
        $zipId = $this->getRequest()->getParam('id');
        /** @var \Custom\City\Model\Zip $model */
        $model = $this->_zipFactory->create();

        if ($zipId) {
            $model->load($zipId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This neighborhoods no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getZipData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('city_zip', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::zip');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Veighborhoods'));

        return $resultPage;
    }
}