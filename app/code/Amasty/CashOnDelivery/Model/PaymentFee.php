<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model;

use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;
use Magento\Framework\Model\AbstractModel;
use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;

class PaymentFee extends AbstractModel implements PaymentFeeInterface
{
    public function _construct()
    {
        $this->_init(PaymentFeeResource::class);
    }

    /**
     * @inheritdoc
     */
    public function getQuoteId()
    {
        return $this->_getData(PaymentFeeInterface::QUOTE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(PaymentFeeInterface::QUOTE_ID, $quoteId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return (float)$this->_getData(PaymentFeeInterface::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->setData(PaymentFeeInterface::AMOUNT, $amount);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBaseAmount()
    {
        return (float)$this->_getData(PaymentFeeInterface::BASE_AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setBaseAmount($baseAmount)
    {
        $this->setData(PaymentFeeInterface::BASE_AMOUNT, $baseAmount);

        return $this;
    }
}
