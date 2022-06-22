<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model;

use Amasty\CashOnDelivery\Api\PaymentManagementInterface;
use Amasty\CashOnDelivery\Model\PaymentValidator;

class PaymentManagement implements PaymentManagementInterface
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
    public function checkAvailable($postalCode)
    {
        return $this->paymentValidator->validateBaseOnPostalCode(null, $postalCode);
    }
}
