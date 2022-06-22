<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\ResourceModel\Waybill;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'waybill_id';
	protected $_eventPrefix = 'naqel_shipping_waybill_record_collection';
	protected $_eventObject = 'waybill_collection';

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Naqel\Shipping\Model\Waybill', 'Naqel\Shipping\Model\ResourceModel\Waybill');
	}

}