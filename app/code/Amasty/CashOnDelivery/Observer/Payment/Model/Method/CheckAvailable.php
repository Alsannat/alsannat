<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Observer\Payment\Model\Method;

use Amasty\CashOnDelivery\Model\PaymentValidator;
use Magento\Framework\Event\ObserverInterface;

class CheckAvailable implements ObserverInterface
{
    /**
     * @var PaymentValidator
     */
    private $paymentValidator;

    public function __construct(PaymentValidator $paymentValidator)
    {
        $this->paymentValidator = $paymentValidator;
    }

    /**
     * @inheritdoc
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $methodInstance = $observer->getMethodInstance();

        if ($methodInstance instanceof \Magento\OfflinePayments\Model\Cashondelivery) {
            /** @var \Magento\Quote\Model\Quote $quote */
            if ($quote = $observer->getQuote()) {
                $result = $observer->getResult();
                $result->setIsAvailable(
                    $this->paymentValidator->validateBasedOnShipping($quote)
                    && $this->paymentValidator->validateBaseOnPostalCode($quote)
                );
            }
        }
    }
}
