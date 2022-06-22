<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\ResourceModel\Shipping;
 
 class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'naqel_city_id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'Naqel\Shipping\Model\Shipping',
            'Naqel\Shipping\Model\ResourceModel\Shipping'
        );
    }
}