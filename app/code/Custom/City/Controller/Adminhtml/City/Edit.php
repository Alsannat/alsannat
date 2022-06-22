<?php

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;
class Edit extends City
{

    /**
     * @return void
     */
    public function execute()
    {
        $cityId = $this->getRequest()->getParam('id');
        /** @var \Custom\City\Model\City $model */
        $model = $this->_cityFactory->create();

        if ($cityId) {
            $model->load($cityId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This city no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getCityData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->_coreRegistry->register('city_city', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Cities'));

        return $resultPage;
    }
}