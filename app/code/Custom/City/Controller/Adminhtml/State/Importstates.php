<?php

namespace Custom\City\Controller\Adminhtml\State;
use Custom\City\Controller\Adminhtml\State;

class Importstates extends State
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $isPost = $this->getRequest()->isPost();
        try{
            if ($isPost) {
                $file = $_FILES['import_state'];
                $data = $this->getRequest()->getParam('import_state');
                $this->_getSession()->setFormData($data);
                $country_id = $data['country_id'];
                if (!isset($file['tmp_name']['csv'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $csvProcessor = $this->csv;
                $importProductRawData = $csvProcessor->getData($file['tmp_name']['csv']);
                $counter=1;
                $import = 0;
                $not_exists = '';
                $langs = $this->activeLangs();
                foreach ($importProductRawData as $rowIndex => $dataRow) {

                    if(trim($dataRow[0])!='State' && trim($dataRow[1])!='State Code' && $counter==1){
                        $this->messageManager->addError(__('Columns (State and State Code) are not exists in csv file.'));
                        $this->_redirect('*/*/import');
                        return;
                    }
                    if($counter > 1 && $dataRow[0]!="" && $dataRow[1]!==""){
                        $this->stateCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                        $_states = $this->stateCollection;
                        $_states = $_states->addFieldToFilter('default_name',trim($dataRow[0]))
                            ->addFieldToFilter('country_id', $country_id);

                        if($_states->count() == 0){

                            $data = array('default_name'=>trim($dataRow[0]),'code'=>trim($dataRow[1]),'country_id'=>$country_id);
                            $stateModel = $this->_stateFactory->create();
                            $stateModel->setData($data);
                            try {
                                // Save city
                                $stateModel->save();
                                $last_id = $stateModel->getId();
                                if (count($langs) > 0) {
                                    foreach ($langs as $lang) {
                                        $this->stateLocaleCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                                        $_state_locale = $this->stateLocaleCollection;
                                        $_state_locale = $_state_locale->addFieldToFilter('locale',$lang)->addFieldToFilter('region_id', $last_id);
                                        $name = trim($dataRow[0]);
                                        $data = array('locale'=>$lang,'region_id'=>$last_id,'name'=>$name);
                                        if ($_state_locale->getSize() == 0) {
                                            $this->statelocale->setData($data)->save();
                                        }
                                    }
                                }
                                $import++;
                            }catch (\Exception $e) {
                                $this->messageManager->addError($e->getMessage());
                            }
                        }else{
                            $not_exists.='<br />'.__('State').' <b>"'.$dataRow[0].'"</b> '.__('is already exists in selected country.');
                        }
                    }
                    $counter++;
                }
                if($import > 0){
                    $this->messageManager->addSuccess(__('States imported successfully.').$not_exists);
                }else{
                    $this->messageManager->addError(__('No state imported, either already exists or data is not correct, check your file.').$not_exists);
                }

            }
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/import');
    }
    private function activeLangs(){
        $stores = $this->storeManager->getStores($withDefault = false);
        $locale = [];
        //Try to get list of locale for all stores;
        foreach($stores as $store) {
            $locale[] = $this->scopeConfig->getValue('general/locale/code', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store->getStoreId());
        }
        return $locale;
    }
}