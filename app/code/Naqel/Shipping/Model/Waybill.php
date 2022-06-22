<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model;

class Waybill extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
	const CACHE_TAG = 'naqel_shipping_waybill_record';

	protected $_cacheTag = 'naqel_shipping_waybill_record';

	protected $_eventPrefix = 'naqel_shipping_waybill_record';

	/**
     * Define resource model
     */
	protected function _construct()
	{
		$this->_init('Naqel\Shipping\Model\ResourceModel\Waybill');
	}

	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId()];
	}

	public function getDefaultValues()
	{
		$values = [];

		return $values;
	}
}