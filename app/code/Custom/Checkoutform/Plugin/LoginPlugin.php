<?php

/**
 *
 */
namespace Custom\Checkoutform\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\Redirect;
/**
 *
 */
class LoginPlugin
{

    protected $session;

    public function __construct(
        Session $customerSession,
        Redirect $result        
       ) {
   
           $this->customerSession = $customerSession;
           $this->result = $result;
       }
    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
    public function beforeExecute()
    {
        $this->result->setPath('registration'); // Change this to what you want
        return $this->result;
    }

}