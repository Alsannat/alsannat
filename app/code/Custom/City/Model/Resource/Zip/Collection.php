<?php

namespace Custom\City\Model\Resource\Zip;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\City\Model\Zip',
            'Custom\City\Model\Resource\Zip'
        );
    }


    public function addFieldToFilter($field, $condition = null)
    {
        $conditionSql = $this->_getConditionSql($field, $condition);
        if($field=='city_id'){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $_cities = $objectManager->create('Custom\City\Model\Resource\City\Collection');
            /** Apply filters here */
            $value = trim(str_replace($field.' LIKE','',$conditionSql));
            $_cities = $_cities->addFieldToFilter('city',array('like'=>str_replace("'",'',$value)));
            $_cities->load();
            $city_ids = array();
            foreach($_cities as $city){
                if($city['id'] !=""){
                    $city_ids[] = $city['id'];
                }
            }
            if(count($city_ids) > 0){
                return parent::addFieldToFilter($field, array('IN',$city_ids));
            }
        }
        if($field=='state_id'){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $_states = $objectManager->create('Custom\City\Model\Resource\State\Collection');
            /** Apply filters here */
            $value = trim(str_replace($field.' LIKE','',$conditionSql));
            $_states = $_states->addFieldToFilter('default_name',array('like'=>str_replace("'",'',$value)));
            $_states->load();
            $state_ids = array();
            foreach($_states as $state){
                if($state['region_id'] > 0){
                    $state_ids[] = $state['region_id'];
                }
            }
            if(count($state_ids) > 0){
                return parent::addFieldToFilter('main_table.'.$field, array('IN',$state_ids));
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }

}