<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Ui\Component\Listing\Column;
 
use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Naqel\Shipping\Model\WaybillFactory;
class Waybillnocolumn extends Column
{
 
    protected $_orderRepository;
    protected $_searchCriteria;
    protected $_WaybillFactory;
 
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        WaybillFactory $WaybillFactory,
        array $components = [], array $data = [])
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->_WaybillFactory = $WaybillFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    public function prepareDataSource(array $dataSource)
    {
        // Add new column in sales order grid (Naqel waybill no)
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->_orderRepository->get($item["entity_id"]);
                $order_id = $order->getEntityId();
                $collection = $this->_WaybillFactory->create()->getCollection();
                $collection->addFieldToFilter('entity_id',$order_id);
                $data = $collection->getFirstItem();
                $item[$this->getData('name')] = $data->getWaybillNo();
            }
        }
        return $dataSource;
    }
}