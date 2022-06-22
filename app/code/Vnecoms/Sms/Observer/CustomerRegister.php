<?php
namespace Vnecoms\Sms\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class CustomerRegister implements ObserverInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Email\Model\Template\Filter
     */
    protected $filter;
    
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;
    
    /**
     * @var \Vnecoms\Sms\Model\MobileFactory
     */
    protected $mobileFactory;


    /**  @var TimezoneInterface */
    private $timezone;
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Email\Model\Template\Filter $filter
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Vnecoms\Sms\Model\MobileFactory $mobileFactory
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Email\Model\Template\Filter $filter,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        TimezoneInterface $timezone,
        \Vnecoms\Sms\Model\MobileFactory $mobileFactory
    ){
        $this->helper = $helper;
        $this->filter = $filter;
        $this->customerFactory = $customerFactory;
        $this->timezone = $timezone;
        $this->mobileFactory = $mobileFactory;

    }
    
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(!$this->helper->getCurrentGateway()) return;
        
        $customer = $observer->getCustomer();
        if(!$customer instanceof \Magento\Customer\Model\Customer){
            $customer = $this->customerFactory->create()->load($customer->getId());
        }
        /* Send notification message to admin when a new customer registered*/
        if($this->helper->canSendCustomerRegisterMessageToAdmin()){
            $message = $this->helper->getCustomerRegisterMessageSendToAdmin();
            $this->filter->setVariables(['customer' => $customer]);
            $message = $this->filter->filter($message);
            $this->helper->sendAdminSms($message);
        }
        
        /* Save customer mobile*/
        $controller = $observer->getAccountController();

        $mobileNum = $controller->getRequest()->getParam('customer_mobile');
        $otp = $controller->getRequest()->getParam('otp');

        if(!$mobileNum) return;
        
		"<script>document.write(localStorege.setItem('reg_mobile','".$mobileNum."'))</script>";
		
        $mobile = $this->mobileFactory->create();
        $customer->setData('mobilenumber', $mobileNum)->save();
        
        /* Delete all otp rows relate to mobule num*/
        $collection = $this->mobileFactory->create()->getCollection()
            ->addFieldToFilter('mobile', $mobileNum);
        foreach($collection as $mobile){
            $mobile->delete();
        }
        
        
        if(
            $this->helper->isEnableVerifyingCustomerMobile() &&
            !$this->helper->isEnableVerifyingOnRegister()
        ) {
            return;
        }
        
        /* Send vendor account approved sms message*/
        if(
            (
                !$this->helper->isEnableVerifyingCustomerMobile() ||
                (
                    $this->helper->isEnableVerifyingCustomerMobile() &&
                    $this->helper->isEnableVerifyingOnRegister()
                )
            ) &&
            $this->helper->canSendCustomerRegisterMessage()
        ){
            $message = $this->helper->getCustomerRegisterMessage();
            $this->filter->setVariables(['customer' => $customer]);
            $message = $this->filter->filter($message);
            $this->helper->sendCustomerSms($customer, $message);
        }
        return $this;
    }
}
