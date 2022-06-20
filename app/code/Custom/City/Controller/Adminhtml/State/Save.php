<?php

namespace Custom\City\Controller\Adminhtml\State;

use Custom\City\Controller\Adminhtml\State;

class Save extends State
{

    private function activeLangs(){
        $stores = $this->storeManager->getStores($withDefault = false);
        $locale = [];
        //Try to get list of locale for all stores;
        foreach($stores as $store) {
            $locale[] = $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
        }

        return $locale;
    }
    /**
     * @return void
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();
        if ($isPost) {
            $stateId = 0;
            $arabic_name = '';
            $stateModel = $this->_stateFactory->create();
            $formData = $this->getRequest()->getParam('state');

            if(isset($formData['id'])){
                $formData['region_id'] = $formData['id'];
            }

            if(isset($formData['id'])){
                $stateId = $formData['id'];
            }

            if(isset($formData['arabic_name'])){
                $arabic_name = $formData['arabic_name'];
            }

            $stateModels = $stateModel->getCollection()
                            ->addFieldToFilter('default_name',$formData['default_name'])
                            ->addFieldToFilter('country_id',$formData['country_id']);
            if($stateId > 0){
                $stateModels = $stateModels->addFieldToFilter('region_id',array('neq'=>$stateId));
            }

            if($stateModels->count() > 0){
                $this->messageManager->addError(__('State').' <b>"'.$formData['default_name'].'"</b> '.__('is already exist in selected country.'));
                $this->_getSession()->setFormData($formData);
                
                if ($this->getRequest()->getParam('back')) {
                    if($stateId > 0){
                        $this->_redirect('*/*/edit', ['id' => $stateId, '_current' => true]);
                    }else{
                        $this->_redirect('*/*/new');
                    }
                    return;
                }

                if($stateId > 0){
                    $this->_redirect('*/*/edit', ['id' => $stateId]);
                }else{
                    $this->_redirect('*/*/new');
                }

                return;
            }

            if ($stateId > 0) {
                $stateModel->load($stateId);
            }

            $stateModel->setData($formData);

            try {
                $stateModel->save();
                $last_id = $stateModel->getId();
                $langs = $this->activeLangs();
                if(count($langs) > 0){
                    foreach($langs as $lang){
                        $stateLocale = $this->statelocale->getCollection()
                            ->addFieldToFilter('region_id',$last_id)->addFieldToFilter('locale',$lang);
                        $name = $stateModel->getDefaultName();
                        if($stateLocale->count()==0){
                            if($lang == 'ar_SA'){
                                $lang_data = array('locale'=>$lang,'region_id'=>$last_id,'name'=>$arabic_name);
                            }else{
                                $lang_data = array('locale'=>$lang,'region_id'=>$last_id,'name'=>$name);
                            }
                            $this->statelocale->setData($lang_data)->save();
                        }

                        if(isset($formData['id'])){
                            $connection = $this->resourceConnection;
                            $conn = $connection->getConnection();
                            if($lang == 'ar_SA'){
                                $conn->rawQuery("UPDATE directory_country_region_name set name = '".$arabic_name."' WHERE locale = '".$lang."' AND region_id = '".$last_id."'");
                            }else{
                                $conn->rawQuery("UPDATE directory_country_region_name set name = '".$name."' WHERE locale = '".$lang."' AND region_id = '".$last_id."'");
                            }
                        }
                    }
                }

                $this->messageManager->addSuccess(__('The state has been saved.'));

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', ['id' => $stateModel->getId(), '_current' => true]);
                    return;
                }

                $this->_getSession()->setFormData(null);
                $this->_redirect('*/*/');
                
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->_getSession()->setFormData($formData);
            $this->_redirect('*/*/edit', ['id' => $stateId]);
        }
    }
}