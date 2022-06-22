<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class MassShip extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{

    protected $orderManagement;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Backend\Model\Auth\Session $authSession

    ) {
        parent::__construct($context, $filter);
        $this->authSession = $authSession;
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
    }

    //***// //***//
        // This will perform mass shipment and SalesOrderShipmentBefore observer will also be called for every order to register order data into Naqel API and store Waybill_no into database table naqel_shipping_waybill_record 
    //***// //***//
    protected function massAction(AbstractCollection $collection)
    {
        $countShipOrder = 0; $NonShipOrdernuumbers = '';
        $model = $this->_objectManager->create('Magento\Sales\Model\Order');

        $username = $this->authSession->getUser()->getUsername();
        $appendusername = "(".$username.")";

        foreach ($collection->getItems() as $order) {
            if (!$order->getEntityId()) {
                continue;
            }

           //naqelshipping_naqelshipping
            /*if (!$order->hasInvoices() && ($order->getShippingMethod())) 
            {
                $this->messageManager->addError(__('Invoice is not generated for %1 order OR', $order->getIncrementId()));
                continue;
            }*/
           
            $loadedOrder = $model->load($order->getEntityId());

            if($loadedOrder->canShip()) {


            $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
            $shipment = $convertOrder->toShipment($loadedOrder);

            // Loop through order items
            foreach ($order->getAllItems() AS $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = $orderItem->getQtyToShip();
                // Create shipment item with qty
                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                // Add shipment item to shipment
                $shipment->addItem($shipmentItem);
            }

            // Register shipment
            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);

            try {
                // Save created shipment and order
                $shipment->save();
                $shipment->getOrder()->save();
                //send notification code
                $loadedOrder->addStatusHistoryComment(
                __('Notified customer about shipment #%1. '.$appendusername, $shipment->getId())
                )->setIsCustomerNotified(false)->save();

                $itemsCheck = $loadedOrder->getItemsCollection()->addAttributeToSelect('*')->load();
                foreach ($itemsCheck as $item) {
                    if (! $item->getQtyToShip() || $item->getIsVirtual()) { 
                    continue;
                    }
                    $item->setQtyShipped($item->getQtyToShip());
                    $item->save();
                    $Norder = $shipment->getOrder()->load( $shipment->getOrder()->getId() );
                    $Norder->save();
                }

                // Send email
                // Uncomment if you also want to send an e-mail
                /*$this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                    ->notify($shipment);*/

                $shipment->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
            }

            if ($loadedOrder->canInvoice()) {
            $loadedOrder->setState(\Magento\Sales\Model\Order::STATE_PROCESSING, true);
            $loadedOrder->setStatus('shipped');
            $loadedOrder->addStatusToHistory('shipped', 'Order status set to shipped using Mass Ship action. '.$appendusername);
            $loadedOrder->save();
            }

            $countShipOrder++;
            }
            else {
                if (empty($NonShipOrdernuumbers)){
                $NonShipOrdernuumbers = $NonShipOrdernuumbers.$loadedOrder->getIncrementId();
                }
                else{
                $NonShipOrdernuumbers = $NonShipOrdernuumbers.", ".$loadedOrder->getIncrementId();  
                }
            }
        }
        $countNonShipOrder = $collection->count() - $countShipOrder;

        if ($countNonShipOrder && $countShipOrder) {
            $this->messageManager->addSuccess(__('%1 order(s) Shipment created successfully.', $countShipOrder));
            $this->messageManager->addError(__('Shipment already created for %1 order(s).', $NonShipOrdernuumbers));
        } elseif ($countNonShipOrder) {
            $this->messageManager->addError(__('Shipment already created for %1 order(s).', $NonShipOrdernuumbers));
        }

        if ($countShipOrder) {
            $this->messageManager->addSuccess(__('%1 order(s) Shipment created successfully.', $countShipOrder));
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
}