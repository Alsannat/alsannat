<?php

namespace Evincemage\City\Model\ResourceModel\District;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    
    protected $_idFieldName = 'district_id';
    
    protected function _construct()
    {
        $this->_init(
            'Evincemage\City\Model\District',
            'Evincemage\City\Model\ResourceModel\District'
        );
    }
}
