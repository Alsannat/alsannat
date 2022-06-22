<?php
namespace Lotus\Override\Model\ResourceModel\Order\Grid;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;
use Psr\Log\LoggerInterface as Logger;


class Collection extends OrderGridCollection
{
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = Order::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }
    protected function _renderFiltersBefore() {
        
        $joinTable = $this->getTable('sales_order_address');
        $joinTable_track = $this->getTable('sales_shipment_track');
        $this->getSelect()->joinLeft(
            $joinTable, 'main_table.entity_id = sales_order_address.parent_id AND sales_order_address.address_type = "shipping"', 
            ['telephone','region','city']
        );

        $this->getSelect()->joinLeft(
            'sales_order', 'main_table.entity_id = sales_order.entity_id', 
            ['coupon_code','awd_status']
        );

        $this->getSelect()->joinLeft(
            $joinTable_track, 'main_table.entity_id = sales_shipment_track.order_id', 
            ['track_number','title']
        )->group('main_table.entity_id');
        
        parent::_renderFiltersBefore();
    }
}