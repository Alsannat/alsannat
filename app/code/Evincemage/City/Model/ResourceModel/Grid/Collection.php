<?php

namespace Evincemage\City\Model\ResourceModel\Grid;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_idFieldName = 'entity_id';
    
    protected function _construct()
    {
        $this->_init(
            'Evincemage\City\Model\Grid',
            'Evincemage\City\Model\ResourceModel\Grid'
        );
    }
}
