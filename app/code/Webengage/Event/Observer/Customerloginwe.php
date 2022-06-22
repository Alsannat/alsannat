<?php

namespace Webengage\Event\Observer;

class Customerloginwe implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Customerloginwe constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(\Magento\Customer\Model\Session $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customerSession = $this->customerSession;
        $customerSession->setCustomerloggedin('yes');
        setcookie('customerLoggedIn', 'yes', time() + (86400 * 30), "/"); // 86400 = 1 day
    }


}