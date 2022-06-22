<?php
/**
 * Copyright © 2015 MGS. All rights reserved.
 */
namespace MGS\Popup\Model\ResourceModel;

/**
 * Popup resource
 */
class Popup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	protected $_store = null;
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    )
    {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('mgs_popup', 'popup_id');
    }
	
	protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['popup_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('mgs_popup_store'), $condition);
        return parent::_beforeDelete($object);
    }
	
	protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStores();
        if (empty($newStores)) {
            $newStores = (array)$object->getStoreId();
        }
        $table = $this->getTable('mgs_popup_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = ['popup_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['popup_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
		
        return parent::_afterSave($object);
    }
	
	public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        return parent::load($object, $value, $field);
    }
	
	protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
			
			$customerGroup = $this->lookupCustomerGroup($object->getId());
			$object->setData('customer_group', $customerGroup);
        }
        return parent::_afterLoad($object);
    }
	
	protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int)$object->getStoreId()];
            $select->join(
                ['mgs_popup_store' => $this->getTable('mgs_popup_store')],
                $this->getMainTable() . '.popup_id = mgs_popup_store.popup_id',
                []
            )->where(
                'status = ?',
                1
            )->where(
                'mgs_popup_store.store_id IN (?)',
                $storeIds
            )->order(
                'mgs_popup_store.store_id DESC'
            )->limit(
                1
            );
        }
        return $select;
    }

	public function lookupStoreIds($popupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('mgs_popup_store'),
            'store_id'
        )->where(
            'popup_id = ?',
            (int)$popupId
        );
        return $connection->fetchCol($select);
    }

	public function lookupCustomerGroup($popupId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('mgs_popup'),
            'customer_group'
        )->where(
            'popup_id = ?',
            (int)$popupId
        );
		
		$customerGroup = unserialize($connection->fetchCol($select)[0]);
		
        return $customerGroup;
    }
  
}
