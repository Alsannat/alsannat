<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\ResourceModel;

class Shipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init('naqel_shipping_naqel_city', 'naqel_city_id');   
        //here "naqel_shipping_naqel_city" is table name and "naqel_city_id" is the primary key of custom table
    }
    
}