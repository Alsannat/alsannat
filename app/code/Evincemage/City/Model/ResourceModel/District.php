<?php

namespace Evincemage\City\Model\ResourceModel;

class District extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected $_idFieldName = 'district_id';

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        
    }
    
    protected function _construct()
    {
        $this->_init('courier_manager_districts', 'district_id');
    }
}
