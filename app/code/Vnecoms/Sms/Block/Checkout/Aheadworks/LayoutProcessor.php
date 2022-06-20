<?php

namespace Vnecoms\Sms\Block\Checkout\Aheadworks;

/**
 * Class LayoutProcessor
 */
class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Vnecoms\Sms\Helper\Data
     */
    protected $helper;
    
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    
    /**
     * @param \Vnecoms\Sms\Helper\Data $helper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Vnecoms\Sms\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->customerSession = $customerSession;
    }
    
    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dev11111.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info($jsLayout['components']['checkout']['children']['shippingAddress']['children']);
        if(!$this->helper->getCurrentGateway()) return $jsLayout;

        /*Shipping mobile*/
        $telephoneData = $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-5']['children']['telephone']['config'];
        $telephoneData['elementTmpl'] = 'Vnecoms_Sms/checkout/mobile';
        $telephoneData['initialCountry'] = strtolower($this->helper->getInitialCountry());
        $telephoneData['geoIpUrl'] = $this->helper->getGeoIpDatabase()?$this->urlBuilder->getUrl('vsms/geoip'):'https://ipinfo.io';
        $allowedCountries = $this->helper->isAllowedAllCountries();
        $telephoneData['onlyCountries'] = $allowedCountries?[]:$allowedCountries;
        $preferredCountries = $this->helper->getPreferredCountries();
        $preferredCountries = $preferredCountries?explode(',', $preferredCountries):["us", "vn"];
        $telephoneData['preferredCountries'] = $preferredCountries;
        $telephoneData['requireVerifying'] = $this->helper->isEnableVerifyingAddressMobile();
        $telephoneData['sendOtpUrl'] = $this->urlBuilder->getUrl('vsms/otp_checkout/send');
        $telephoneData['verifyOtpUrl'] = $this->urlBuilder->getUrl('vsms/otp_checkout/verify');
        $telephoneData['otpResendPeriodTime'] = $this->helper->getOtpResendPeriodTime();
        $telephoneData['defaultResendBtnLabel'] = __('Resend');
        if($this->customerSession->isLoggedIn()){
            $telephoneData['customerMobileNumber'] = $this->customerSession->getCustomer()->getMobilenumber();
        }

        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-5']['children']['telephone']['component'] = 'Vnecoms_Sms/js/checkout/mobile';
        $jsLayout['components']['checkout']['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['field-row-5']['children']['telephone']['config'] = $telephoneData;
        
        return $jsLayout;
    }
}
