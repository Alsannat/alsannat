<?php

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;
class Edit extends State
{

    /**
     * @return void
     */
    public function execute()
    {
        $stateId = $this->getRequest()->getParam('id');
        /** @var \Custom\City\Model\State $model */
        $model = $this->_stateFactory->create();
        if (isset($stateId)) {
            $model->load($stateId);
            $region_id = $model->getData('region_id');
            $data = $this->getRegionNameByAr($region_id);
            $model->setData('arabic_name',$data['name']);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This state no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $data = $this->_session->getStateData(true);
        
        if (!empty($data)) {
            $model->setData($data);
        }
        
        $this->_coreRegistry->register('city_state', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage States'));

        return $resultPage;
    }

    function getRegionNameByAr($region_id){
        $locale = 'ar_SA';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        $check_sql = "SELECT * FROM directory_country_region_name WHERE region_id = '".$region_id."' AND locale = '".$locale."' LIMIT 1";
        $result = $connection->fetchAll($check_sql);

        if (count($result) > 0) {
            return $result[0];
        }
    }
}