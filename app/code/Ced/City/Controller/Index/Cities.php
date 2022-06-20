<?php
 
namespace Ced\City\Controller\Index;
 
use Ced\City\Controller\City;
 
class Cities extends City
{
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resultJsonFactory=$objectManager->get('Magento\Framework\Controller\Result\JsonFactory');
        $state_id = $this->getRequest()->getParam('state');
		$cities = array();
		$cities_options = $this->_cityFactory->create()->getCollection()->setOrder('city', 'asc');
       //$city =  $cities_options->getColumnValues('city_code');
        $options = [];
        $i = 0;
        foreach ($cities_options as $option){
            $options[$i]['city_code'] = $option->getCityCode();
            $options[$i]['city_label'] = $option->getCity();
            $i++;
        }
        $resultJson =$resultJsonFactory->create();
        return $resultJson->setData($options);
    
    }
}