<?php

namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class Save extends Zip
{
    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();

        if ($isPost) {
            $zipId = 0;
            $zipModel = $this->_zipFactory->create();
            $formData = $this->getRequest()->getParam('zip');
            if(isset($formData['id'])){
                $zipId = $formData['id'];
            }
            $zipModels = $zipModel->getCollection()
                ->addFieldToFilter('zip_name',$formData['zip_name'])
                ->addFieldToFilter('zip_name_ar',$formData['zip_name_ar'])
                ->addFieldToFilter('city_id',$formData['city_id'])
                ->addFieldToFilter('state_id',$formData['state_id'])
                ->addFieldToFilter('country_id',$formData['country_id']);
            if($zipId > 0){
                $zipModels = $zipModels->addFieldToFilter('id',array('neq'=>$zipId));
            }
            if($zipModels->count() > 0){
                $this->messageManager->addError(__('Zip code').' <b>"'.$formData['zip_name'].'"</b> '.__('is already exist in selected city.'));
                $this->_getSession()->setFormData($formData);
                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    if($zipId > 0){
                        $this->_redirect('*/*/edit', ['id' => $zipId, '_current' => true]);
                    }else{
                        $this->_redirect('*/*/new');
                    }
                    return;
                }
                if($zipId > 0){
                    // Go to grid page
                    $this->_redirect('*/*/edit', ['id' => $zipId]);
                }else{
                    $this->_redirect('*/*/new');
                }
                return;
            }
            if ($zipId > 0) {
                $zipModel->load($zipId);
            }else{
                $formData['created_at'] = date('Y-m-d');
            }

            $zipModel->setData($formData);

            try {
                // Save zip
                $zipModel->save();

                // Display success message
                $this->messageManager->addSuccess(__('The zip code has been saved.'));

                // Check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $zipModel->getId(), '_current' => true]);
                    return;
                }
                $this->_getSession()->setFormData(null);
                // Go to grid page
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $zipId]);
        }
    }
}