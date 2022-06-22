<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Popup\Model\ResourceModel\Popup;

/**
 * Popups Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('MGS\Popup\Model\Popup', 'MGS\Popup\Model\ResourceModel\Popup');
    }
	
	
	public function addStoreFilter($store, $adminStore = true) {
		$stores = array();

		if ($store instanceof \Magento\Store\Model\Store) {
            $stores[] = (int)$store->getId();
        }
		
		$stores[] = 0;
		$storeTable = $this->getTable('mgs_popup_store');
        $this->getSelect()->join(
                        array('stores' => $storeTable),
                        'main_table.popup_id = stores.popup_id',
                        array()
                )
                ->where('stores.store_id in (?)', ($adminStore ? $stores : $store));
        return $this;
    }
}
