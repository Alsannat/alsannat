<?php
 
namespace Custom\City\Model\Resource;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class State extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('directory_country_region', 'region_id');
    }
}