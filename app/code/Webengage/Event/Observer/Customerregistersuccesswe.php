<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Customerregistersuccesswe implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Backend\Model\Session
     */
    private $session;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * Customerregistersuccesswe constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Customer $customer
     * @param Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Backend\Model\Session $session,
        Data $helper)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->session = $session;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $storeManager = $this->storeManager;
        $storeName = $storeManager->getStore()->getName();
        $catalogSession = $this->session;
        $customer = $event->getCustomer();
        $customerEmail = $customer->getEmail();
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $customerEmail = $customerSession->getCustomer()->getEmail();
        }
        $_SESSION['customersignup'] = 'yes';
        $catalogSession->setSignuplogin('yes');
        $customerSession->setSignuplogindata('yes');
        setcookie('customerSignupLoggedIn', 'yes', time() + (86400 * 30), "/"); // 86400 = 1 day

        $isdCode = isset($_POST['isd_code']) ? $_POST['isd_code'] : '';
        $primaryMobileNo = isset($_POST['primary_mobile_number']) ? $_POST['primary_mobile_number'] : '';

        $email = $customer->getEmail();
        $firstName = $customer->getFirstname();
        $lastName = $customer->getLastname();

        $websiteId = $storeManager->getWebsite()->getWebsiteId();
        $CustomerModel = $this->customer;
        $CustomerModel->setWebsiteId($websiteId);
        $CustomerModel->loadByEmail($email);
        $customerData = (object)$CustomerModel->getData();

        $prepareJson = array(
                'event_name' => 'User Signed Up',
                'cuid' => $customerEmail,
                'event_data' => array(
                    'we_first_name' => $firstName,
                    'we_last_name' => $lastName,
                    'we_email' => $email,
                    'we_phone' => '+' . '' . $isdCode . '' . $primaryMobileNo,
                    'phone' => '+' . '' . $isdCode . '' . $primaryMobileNo,
                    'customerCreatedStoreName' => $storeName,
                    'customerPrimaryNumber' => $primaryMobileNo,
                    'customerIsdCode' => $isdCode,
                    'customerFullNumber' => $isdCode . $primaryMobileNo,
                    'customerCreatedAt' => $customerData->created_at,
                )
        );

        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/
        return $this;
    }


}
