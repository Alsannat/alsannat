<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model;

class ConfigProvider extends \Amasty\Base\Model\ConfigProviderAbstract
{
    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = 'payment/';

    /**#@+
     * xpath group parts
     */
    const CASH_ON_DELIVERY_BLOCK = 'cashondelivery/';
    /**#@-*/

    /**#@+
     * xpath field parts
     */
    const CASH_ON_DELIVERY_ENABLED = 'active';

    const PAYMENT_FEE_ENABLED = 'enable_payment_fee';
    const PAYMENT_FEE_LABEL = 'payment_fee_label';
    const PAYMENT_FEE = 'payment_fee';
    const PAYMENT_FEE_FREE = 'payment_fee_free';
    const PAYMENT_FEE_TYPE = 'payment_fee_type';

    const CALCULATE_BASED_ON = 'payment_calculate_based_on';
    const ALLOWED_SHIPPING = 'payment_shipping_allowspecific';
    const SPECIFIC_SHIPPING = 'payment_shipping_specificcountry';

    const CODE_VERIFICATION_ENABLED = 'enable_postcode_verification';
    const VERIFICATION_TYPE = 'postcode_verification_type';
    const ALLOWED_POSTAL_CODES = 'allowed_postal_codes';

    const ORDER_STATUS = 'order_status';
    /**#@-*/

    /**
     * @return bool
     */
    public function isPaymentFeeEnabled()
    {
        return $this->isSetFlag(self::CASH_ON_DELIVERY_BLOCK . self::PAYMENT_FEE_ENABLED);
    }

    /**
     * @return bool
     */
    public function isCashOnDeliveryEnabled()
    {
        return $this->isSetFlag(self::CASH_ON_DELIVERY_BLOCK . self::CASH_ON_DELIVERY_ENABLED);
    }

    /**
     * @return bool
     */
    public function isCodeVerificationEnabled()
    {
        return $this->isSetFlag(self::CASH_ON_DELIVERY_BLOCK . self::CODE_VERIFICATION_ENABLED);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPaymentFeeLabel()
    {
        $paymentFeeLabel = $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::PAYMENT_FEE_LABEL)
            ?: 'Cash on Delivery Fee';

        return __($paymentFeeLabel);
    }

    /**
     * @return string
     */
    public function getPaymentFee()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::PAYMENT_FEE);
    }
    /**
     * @return string
     */
    public function getPaymentFeeFree()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::PAYMENT_FEE_FREE);
    }

    /**
     * @return string
     */
    public function getAllowedShipping()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::ALLOWED_SHIPPING);
    }

    /**
     * @return string
     */
    public function getSpecificShipping()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::SPECIFIC_SHIPPING);
    }

    /**
     * @return string
     */
    public function getVerificationType()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::VERIFICATION_TYPE);
    }

    /**
     * @return string
     */
    public function getAllowedPostalCodes()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::ALLOWED_POSTAL_CODES);
    }

    /**
     * @return string
     */
    public function getOrderStatus()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::ORDER_STATUS);
    }

    /**
     * @return int
     */
    public function getPaymentFeeType()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::PAYMENT_FEE_TYPE);
    }

    /**
     * @return int
     */
    public function getCalculateBasedOn()
    {
        return $this->getValue(self::CASH_ON_DELIVERY_BLOCK . self::CALCULATE_BASED_ON);
    }
}
