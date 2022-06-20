<?php

namespace Custom\City\Controller\Adminhtml\Zip;
use Custom\City\Controller\Adminhtml\Zip;

class Importzips extends Zip
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dev16_import.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info('Your text message');
        $isPost = $this->getRequest()->isPost();
        try{
            if ($isPost) {
                $file = $_FILES['import_zip'];
                $data = $this->getRequest()->getParam('import_zip');
                $this->_getSession()->setFormData($data);

                $country_id = $data['country_id'];$logger->info($country_id);
                $state_id = $data['state_id'] > 0 ? $data['state_id'] : 743 ;$logger->info($state_id);
                if (!isset($file['tmp_name']['csv'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }
                $csvProcessor = $this->csv;
                $importProductRawData = $csvProcessor->getData($file['tmp_name']['csv']);
                $counter=1;
                $import = 0;
                $not_exists = '';
                foreach ($importProductRawData as $rowIndex => $dataRow) {
                    //$logger->info(print_r($dataRow,true));
                    if((isset($dataRow[0]) && isset($dataRow[1])) && trim($dataRow[0])!='Zipcode' && trim($dataRow[1]!='City') && $counter==1){
                        $this->messageManager->addError(__('Columns (Zipcode and City) are not exists in csv file.'));
                        $this->_redirect('*/*/import');
                        return;
                    }
                    if($counter > 1 && (isset($dataRow[0]) && isset($dataRow[1])) && $dataRow[0]!="" && $dataRow[1]!=""){
                        if(isset($dataRow[2]) && trim($dataRow[2])!="" && $state_id==0){
                            $logger->info('1____');
                            $this->stateCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                            $_states = $this->stateCollection
                                ->addFieldToFilter('default_name',trim($dataRow[2]))
                                ->addFieldToFilter('country_id', $country_id)->load();
                            if($_states->count() > 0) {
                                $state_data = $_states->getFirstItem();
                                $state_id = $state_data->getRegionId();
                            }
                        }
                        $this->cityCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                        $_cities = $this->cityCollection;
                        //$logger->info('$dataRow[1] ___ '.trim($dataRow[1]));
                        $_cities = $_cities->addFieldToFilter('city',trim($dataRow[1]))->addFieldToFilter('state_id',$state_id)
                            ->addFieldToFilter('country_id',$country_id);
                        $logger->info($_cities->getSelect()->__toString());
                        if($_cities->count() > 0){
                            $city_data = $_cities->getFirstItem();
                            $city_id = $city_data->getId();
                            $this->zipCollection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                            $zip_check = $this->zipCollection;
                            $zip_check = $zip_check->addFieldToFilter('city_id',$city_id)
                                ->addFieldToFilter('state_id',$state_id)
                                ->addFieldToFilter('country_id',$country_id)
                                ->addFieldToFilter('zip_name', trim($dataRow[0]));
                            //if($zip_check->count() == 0){
                                $data = array('zip_name'=>trim($dataRow[0]),'city_id'=>$city_id,'state_id'=>$state_id,'country_id'=>$country_id,'status'=>1,'created_at'=>date('Y-m-d'));
                                $zipModel = $this->_zipFactory->create();
                                $zipModel->setData($data);
                                try {
                                    // Save zip
                                    $zipModel->save();
                                    $import++;
                                }catch (\Exception $e) {
                                    $this->messageManager->addError($e->getMessage());
                                }
                            //}else{
                                //$data = array('zip_name'=>trim($dataRow[0]),'city_id'=>'','state_id'=>'','country_id'=>$country_id,'status'=>1,'created_at'=>date('Y-m-d'));
                                //$zipModel->save();
                                //$not_exists.='<br />'.__('Zip code').' <b>"'.$dataRow[0].'"</b> '.__('is already exists.');
                            //}
                        }else{
                            $not_exists.='<br />'.__('City').' <b>"'.$dataRow[1].'"</b> '.__('is not exists in selected country.');
                        }
                    }
                    $counter++;
                }
                if($import > 0){
                    $this->messageManager->addSuccess(__('Zip codes imported successfully.').$not_exists);
                }else{
                    $this->messageManager->addError(__('No zip code imported, either already exists or data is not correct, check your file.').$not_exists);
                }

            }
        }catch(\Exception $e){
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/import');
    }

}