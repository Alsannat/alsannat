<?php
namespace Custom\Checkoutform\Observer;
use Magento\Framework\Event\ObserverInterface;


class CheckoutObserver implements ObserverInterface
{
        
        protected $session;

        /**
         * Customer session
         *
         * @var \Magento\Customer\Model\Session
         */
        protected $_customerSession;

        public function __construct(
            \Magento\Customer\Model\Session $customerSession,
            \Custom\Checkoutform\Block\Session $session

        ) {

            $this->_customerSession = $customerSession;
            $this->session = $session;

        }

        public function execute(\Magento\Framework\Event\Observer $observer)
        {

            if(!$this->_customerSession->isLoggedIn()) {

            $checkout =  $this->session->setValue("checkout");
                $guest =  $this->session->setGuest("guest");
            }

        }

}