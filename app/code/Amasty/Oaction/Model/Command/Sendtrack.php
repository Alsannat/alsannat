<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


declare(strict_types=1);

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Model\Command;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use Magento\Shipping\Model\ShipmentNotifier;

class Sendtrack extends Command
{
    /**
     * @var ShipmentCollectionFactory
     */
    private $shipmentCollectionFactory;

    /**
     * @var ShipmentNotifier
     */
    private $shipmentNotifier;

    public function __construct(
        ShipmentCollectionFactory $shipmentCollectionFactory,
        ShipmentNotifier $shipmentNotifier
    ) {
        parent::__construct();
        $this->shipmentCollectionFactory = $shipmentCollectionFactory;
        $this->shipmentNotifier = $shipmentNotifier;
    }

    /**
     * @param AbstractCollection $collection
     * @param int                $notifyCustomer
     * @param array              $oaction
     *
     * @return string
     */
    public function execute(AbstractCollection $collection, int $notifyCustomer, array $oaction): string
    {
        $orderIds = $collection->getAllIds();
        $shipments = $this->shipmentCollectionFactory->create()->setOrderFilter(['in' => $orderIds]);
        $totalShipments = $shipments->count();
        $totalFailedEmailSend = 0;

        foreach ($shipments as $shipment) {
            $sent = $this->shipmentNotifier->notify($shipment);

            if (!$sent) {
                $this->_errors[] = __(
                    'Can not send the tracking information for shipment %1, please check the error at the log file',
                    $shipment->getIncrementId()
                );
                $totalFailedEmailSend++;
            }
        }

        return __(
            'Tracking information has been successfully sent for %1 of %2 shipments',
            ($totalShipments - $totalFailedEmailSend),
            $totalShipments
        )->render();
    }
}
