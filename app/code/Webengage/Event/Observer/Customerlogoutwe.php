<?php

namespace Webengage\Event\Observer;

class Customerlogoutwe implements \Magento\Framework\Event\ObserverInterface {

    public function execute(\Magento\Framework\Event\Observer $observer) {

        setcookie('customerLoggedOut', 'yes', time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie('customerSignupLoggedIn', null, -1, '/');
        setcookie('customerLoggedIn', null, -1, '/');
    }

}
