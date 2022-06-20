<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model;

use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Customer\Model\Address\AbstractAddress;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Quote\Api\Data\CartInterface;

class PaymentValidator
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param CartInterface $quote
     *
     * @return bool
     */
    public function validatePaymentFee(CartInterface $quote)
    {
        if (!$this->configProvider->isPaymentFeeEnabled() || !$this->configProvider->isCashOnDeliveryEnabled()) {
            return false;
        }

        $paymentMethod = $quote->getPayment()->getMethod() ?: $quote->getPaymentMethod();

        if ($paymentMethod !== Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE) {
            return false;
        }

        return $this->validateBasedOnShipping($quote) && $this->validateBaseOnPostalCode($quote);
    }

    /**
     * @param CartInterface $quote
     *
     * @return bool
     */
    public function validateBasedOnShipping(CartInterface $quote)
    {
        if (!$this->configProvider->isCashOnDeliveryEnabled()) {
            return false;
        }

        if (!$this->configProvider->getAllowedShipping()) {
            return true;
        }

        $specificMethod = $this->configProvider->getSpecificShipping();

        if (!$specificMethod) {
            return true;
        }

        $specificMethod = explode(',', $specificMethod);

        return in_array($quote->getShippingAddress()->getShippingMethod(), $specificMethod);
    }

    /**
     * @param CartInterface $quote
     * @param string|null $zipCode
     *
     * @return bool
     */
    public function validateBaseOnPostalCode(CartInterface $quote = null, $zipCode = null)
    {
        if (!$this->configProvider->isCodeVerificationEnabled()) {
            return true;
        }

        $allowedCodes = $this->configProvider->getAllowedPostalCodes();

        if (!$allowedCodes) {
            return true;
        }

        $allowedCodes = explode(',', $allowedCodes);
        array_walk($allowedCodes, 'trim');
        $verificationType = $this->configProvider->getVerificationType();

        if ($zipCode) {
            $postCode = $zipCode;
        } else {
            if ($verificationType === AbstractAddress::TYPE_BILLING) {
                /** @var \Magento\Quote\Model\Quote\Address $address */
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }

            $postCode = $address->getPostcode();
        }

        return array_search($postCode, $allowedCodes, true) !== false;
    }
}
