<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Api\Data;

interface PaymentFeeInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';
    /**#@-*/

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $entityId
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     */
    public function setEntityId($entityId);

    /**
     * @return int|null
     */
    public function getQuoteId();

    /**
     * @param int|null $quoteId
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     */
    public function setQuoteId($quoteId);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getBaseAmount();

    /**
     * @param float $baseAmount
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     */
    public function setBaseAmount($baseAmount);
}
