<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\ResourceModel;

class Waybill extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context
	)
	{
		parent::__construct($context);
	}
	/**
     * Define main table
     */

	protected function _construct()
	{
		$this->_init('naqel_shipping_waybill_record', 'waybill_id');
		//here "naqel_shipping_waybill_record" is table name and "waybill_id" is the primary key of custom table
	}
	
}