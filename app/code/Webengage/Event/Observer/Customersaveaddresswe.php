<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Customersaveaddresswe implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;


    /**
     * Customersaveaddresswe constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Model\Session $customerSession,
        Data $helper)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->customerSession = $customerSession;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerEmail = null;
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $customerEmail = $customerSession->getCustomer()->getEmail();
        }
        $customerAddress = $observer->getEvent()->getCustomerAddress();
        $customerAddress = (object)$customerAddress->getData();

        $regionName = '';
        if (is_numeric($customerAddress->region_id)) {
            $regionName = $customerAddress->region;
        }
        $prepareJson = array(
                'event_name' => 'Added New Address',
                'event_data' => array(
                    'customerFirstName' => $customerAddress->firstname,
                    'customerLastName' => $customerAddress->lastname,
                    'customerStreet' => $customerAddress->street,
                    'customerTelephone' => $customerAddress->telephone,
                    'customerPostCode' => $customerAddress->postcode,
                    'customerEmail' => $customerEmail,
                    'customerRegionName' => $regionName,
                    'customerRegion' => $customerAddress->region,
                    'customerCountry' => $customerAddress->country_id,
                )
        );
        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/

        // Set the session variable
        $customerSession->setWeUpdatePhone('yes');

        return $this;
    }


}
