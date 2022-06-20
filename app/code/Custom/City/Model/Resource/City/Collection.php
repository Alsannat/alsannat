<?php

namespace Custom\City\Model\Resource\City;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Custom\City\Model\City',
            'Custom\City\Model\Resource\City'
        );
    }

    public function addFieldToFilter($field, $condition = null)
    {
        $conditionSql = $this->_getConditionSql($field, $condition);
        if($field=='state_id'){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $_states = $objectManager->create('Custom\City\Model\Resource\State\Collection');
            /** Apply filters here */
            $value = trim(str_replace($field.' LIKE','',$conditionSql));
            if(is_string($value)){
                $_states = $_states->addFieldToFilter('default_name',array('like'=>str_replace("'",'',$value)));
                $_states->load();
                $state_ids = array();
                foreach($_states as $state){
                    if($state['region_id'] > 0){
                        $state_ids[] = $state['region_id'];
                    }
                }
                if(count($state_ids) > 0){
                    return parent::addFieldToFilter($field, array('IN',$state_ids));
                }
            }
        }
        return parent::addFieldToFilter($field, $condition);
    }
}