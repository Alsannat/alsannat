<?php
 
namespace Custom\City\Model\Resource;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class City extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('cities', 'id');
    }
}