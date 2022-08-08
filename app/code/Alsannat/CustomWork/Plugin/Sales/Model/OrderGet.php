<?php

namespace Alsannat\CustomWork\Plugin\Sales\Model;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;

class OrderGet
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtensionFactory;

    /**
     * Init plugin
     *
     * @param OrderExtensionFactory $orderExtensionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface $resultOrder
    ) {
        $extensionAttributes = $resultOrder->getExtensionAttributes();
        $invoiceId = '';
        $trackTitle = '';
        $trackNumber = '';
        $addressLine1 = '';
        $addressLine2 = '';

        $shippingAddress = $resultOrder->getShippingAddress();

        if(isset($shippingAddress->getStreet()[0]) && isset($shippingAddress->getStreet()[1])){
           $addressLine1 = $shippingAddress->getStreet()[0];
           $addressLine2 = $shippingAddress->getStreet()[1];
        }else{
           $addressLine1 = $shippingAddress->getStreet()[0];
        }

        if(isset($resultOrder->getInvoiceCollection()->getData()[0]['entity_id'])){
          $invoiceId = $resultOrder->getInvoiceCollection()->getData()[0]['entity_id'];
        }
        if(count($resultOrder->getTracksCollection())){
            $trk = $resultOrder->getTracksCollection()->fetchItem();
            $trackTitle = $trk->getTitle();
            $trackNumber = $trk->getTrackNumber();
        }

        /** @var \Magento\Sales\Api\Data\OrderExtension $orderExtension */
        $orderExtension = $extensionAttributes ? $extensionAttributes : $this->orderExtensionFactory->create();
        $orderExtension->setInvoiceId($invoiceId);
        $orderExtension->setShippingTitle($trackTitle);
        $orderExtension->setShippingTrackingNumber($trackNumber);
        $orderExtension->setAddressLine1($addressLine1);
        $orderExtension->setAddressLine2($addressLine2);
        $resultOrder->setExtensionAttributes($orderExtension);

        return $resultOrder;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param Collection $resultOrder
     * @return Collection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(
        OrderRepositoryInterface $subject,
        Collection $resultOrder
    ) {
        /** @var  $order */
        foreach ($resultOrder->getItems() as $order) {
            $this->afterGet($subject, $order);
        }
        return $resultOrder;
    }
}