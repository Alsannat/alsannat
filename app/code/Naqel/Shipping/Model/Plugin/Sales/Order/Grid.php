<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Plugin\Sales\Order;

class Grid
{
    public static $table = 'sales_order_grid';
    public static $leftJoinTable = 'naqel_shipping_waybill_record';
    
    /**
     * filter order grid for waybill_no column 
     */
    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {

            $leftJoinTableName = $collection->getConnection()->getTableName(self::$leftJoinTable);
            $collection
                ->getSelect()
                ->joinLeft(
                    ['co'=>$leftJoinTableName],
                    "co.entity_id = main_table.entity_id",
                    [
                        'waybill_no' => 'co.waybill_no'
                    ]
                );
            $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
            $collection->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $where); 
        }
        return $collection;
    }
}