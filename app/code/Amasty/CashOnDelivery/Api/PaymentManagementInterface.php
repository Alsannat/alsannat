<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Api;

interface PaymentManagementInterface
{

    /**
     * Check cash on delivery for available
     *
     * @param string $postalCode ZIP code.
     * @return bool
     */
    public function checkAvailable($postalCode);
}
