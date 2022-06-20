<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleMapPinAddress\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

class AfterOnePage implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $_order;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressModel;

    /**
     * @var CustomerSession
     */
    protected $customerSession;

     /**
      * @var \Magento\Framework\Session\SessionManagerInterface
      */
    protected $coreSession;

     /**
      * @var \Magento\Quote\Model\Quote\AddressFactory
      */
    protected $address;

      /**
       * @var \Magento\Sales\Model\Order\AddressFactory
       */
    protected $salesAddress;
     
     /**
      * Constructor.
      *
      * @param \Magento\Sales\Api\Data\OrderInterface               $order
      * @param \Magento\Customer\Model\AddressFactory               $addressModel
      * @param CustomerSession                                      $customerSession
      * @param \Magento\Framework\Session\SessionManagerInterface   $coreSession
      * @param \Magento\Quote\Model\Quote\AddressFactory            $address
      * @param \Magento\Sales\Model\Order\AddressFactory            $salesAddress
      */
    
    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Customer\Model\AddressFactory $addressModel,
        CustomerSession $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Quote\Model\Quote\AddressFactory $address,
        \Magento\Sales\Model\Order\AddressFactory $salesAddress
    ) {
         $this->_order = $order;
         $this->addressModel = $addressModel;
         $this->customerSession = $customerSession;
         $this->coreSession = $coreSession;
         $this->address = $address;
         $this->salesAddress =  $salesAddress;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderids = $observer->getEvent()->getOrderIds();

        foreach ($orderids as $orderid) {
            $order = $this->_order->load($orderid);
        }
        $this->coreSession->start();
        $addressCollection = $this->addressModel->create()->getCollection();
        //billing Address Latitude Longitude array
        $billingLatLong =  $this->coreSession->getBillingLatLong();
        //shipping Address Latitude Longitude array
        $shippingLatLong = $this->coreSession->getShippingLatLong();
        $quoteAddressCollection = $this->address->create()->getCollection()
        ->setOrder('quote_id', 'DESC')->setPageSize(2);
        $quoteId = $quoteAddressCollection->getData()[0]['quote_id'];
        $customerId = $this->customerSession->getId();
        //newly created or updated shipping Address Id
        $shippingAddressId = $order->getShippingAddress()->getCustomerAddressId();
        //newly created or updated billing Address Id
        $billingAddressId = $order->getBillingAddress()->getCustomerAddressId();
        if ($shippingAddressId == $billingAddressId ||
        ($billingAddressId && $shippingAddressId == null) ||
        ( $shippingAddressId && $billingAddressId == null)) {
            $userdata =  $addressCollection
            ->addFieldToFilter('entity_id', [$shippingAddressId,$billingAddressId])
            ->addFieldToFilter('parent_id', $customerId);
            foreach ($userdata as $ud) {
                
                $ud->setLatitude($shippingLatLong['latitude']);
                $ud->setLongitude($shippingLatLong['longitude']);
                $ud->save();
            }
        } else {
            //Latitude longitude value saving for shipping address in customer_address_entity table
            $userDataShipping =  $addressCollection
            ->addFieldToFilter('entity_id', $shippingAddressId)
            ->addFieldToFilter('parent_id', $customerId);
            foreach ($userDataShipping as $uds) {
                $uds->setLatitude($shippingLatLong['latitude']);
                $uds->setLongitude($shippingLatLong['longitude']);
                $uds->save();
            }
              //Latitude longitude value saving for billing address in customer_address_entity table
              $addressCollectionForBillnig = $this->addressModel->create()->getCollection();
              $userDataBilling =  $addressCollectionForBillnig
              ->addFieldToFilter('entity_id', $billingAddressId)
              ->addFieldToFilter('parent_id', $customerId);
            foreach ($userDataBilling as $udb) {
                $udb->setLatitude($billingLatLong['latitude']);
                $udb->setLongitude($billingLatLong['longitude']);
                $udb->save();
            }
        }
            //getting quote billing and shipping address Id
        if ($quoteAddressCollection->getData()[0]['address_type'] == 'billing') {
            //getting quote billing address Id from quote_address table
            $quoteBillingAddressId = $quoteAddressCollection->getData()[0]['address_id'];
            //getting quote shipping address Id from quote_address table
            $quoteShippingAddressId = $quoteAddressCollection->getData()[1]['address_id'];
        } else {
            $quoteShippingAddressId = $quoteAddressCollection->getData()[0]['address_id'];
            $quoteBillingAddressId = $quoteAddressCollection->getData()[1]['address_id'];
        }
            //saving the latitude and longitude of billing address in quote_address table
            $quoteCollection = $this->address->create()->getCollection();
            $quoteAddressFilter =  $quoteCollection
            ->addFieldToFilter('quote_id', $quoteId)->addFieldToFilter('address_type', 'billing');
        foreach ($quoteAddressFilter as $qaf) {
          if (isset($billingLatLong['latitude']) && $billingLatLong['latitude']) {
            $qaf->setLatitude($billingLatLong['latitude']);
          }
          if (isset($billingLatLong['longitude']) && $billingLatLong['longitude']) {
            $qaf->setLongitude($billingLatLong['longitude']);
          }
            
            
            $qaf->save();
        }
            //saving the latitude and longitude of shipping address in sales_order_address table
            $salesAddressCollection = $this->salesAddress->create()->getCollection();
            $salesAddressFilterShipping = $salesAddressCollection
            ->addFieldToFilter('quote_address_id', $quoteShippingAddressId);
        foreach ($salesAddressFilterShipping as $safs) {
            $safs->setLatitude($shippingLatLong['latitude']);
            $safs->setLongitude($shippingLatLong['longitude']);
            $safs->save();
        }
            //saving the latitude and longitude of billing address in sales_order_address table
            $salesAddressCollection = $this->salesAddress->create()->getCollection();
            $salesAddressFilterBilling = $salesAddressCollection
            ->addFieldToFilter('quote_address_id', $quoteBillingAddressId);
        foreach ($salesAddressFilterBilling as $safb) {
            $safb->setLatitude($billingLatLong['latitude']);
            $safb->setLongitude($billingLatLong['longitude']);
            $safb->save();
        }
            // unset the billing shipping latitude and longitude in coreSession
                $this->coreSession->unsBillingLatLong();
                $this->coreSession->unsShippingLatLong();
    }
}
