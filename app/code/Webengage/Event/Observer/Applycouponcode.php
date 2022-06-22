<?php

namespace Webengage\Event\Observer;
use Webengage\Event\Helper\Data;

class Applycouponcode implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var Data
     */
    private $helper;
    /**
     * @var \Magento\SalesRule\Model\Coupon
     */
    private $coupon;
    /**
     * @var \Magento\SalesRule\Model\Rule
     */
    private $saleRule;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * Customerloginwe constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
                                \Magento\SalesRule\Model\Coupon $coupon,
                                \Magento\SalesRule\Model\Rule $saleRule,
                                Data $helper,
                                \Magento\Checkout\Model\Cart $cart

)
    {
        $this->helper = $helper;
        $this->coupon = $coupon;
        $this->saleRule = $saleRule;
        $this->cart = $cart;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller = $observer->getControllerAction();

        $couponCode = $controller->getRequest()->getParam('coupon_code');
      $params = $controller->getRequest()->getParams();
        $eventName = 'Coupon Applied';
        if ($controller->getRequest()->getParam('remove') == 1 || $controller->getRequest()->getParam('remove') == '1' || isset($params['remove']) && $params['remove'] ==1) {
            $eventName = 'Coupon Removed';
        }
        $ruleId =   $this->coupon->loadByCode($couponCode)->getRuleId();
        $prepareJson = array();
        if(!empty($ruleId)) {
            $rule = $this->saleRule->load($ruleId);
            $prepareJson = array(
                    'event_name' => $eventName,
                    'event_data' => array(
                        'couponCode' => $couponCode,
                        'couponDiscountAmount' => (float)$rule->getDiscountAmount(),
                        'couponDetail' => $rule->getName(),
                )
            );

        }
        else if($eventName =='Coupon Removed') {
            $cartObj = $this->cart;
            $cartData = (object)$cartObj->getQuote()->getData();
            $couponCode = '';
            if (isset($cartData->coupon_code) && trim($cartData->coupon_code) != '') {
                $couponCode = $cartData->coupon_code;
            }
            $ruleId =   $this->coupon->loadByCode($couponCode)->getRuleId();
            if(!empty($ruleId)) {
                $rule = $this->saleRule->load($ruleId);
                $prepareJson = array(
                    'event_name' => $eventName,
                    'event_data' => array(
                        'couponCode' => $couponCode,
                        'couponDiscountAmount' => (float)$rule->getDiscountAmount(),
                        'couponDetail' => $rule->getName(),
                    )
                );
            }
        }
        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/
        return $this;
    }


}