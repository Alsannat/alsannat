<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Order\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class FeeCollector extends AbstractTotal
{
    /**
     * @var PaymentFeeRepositoryInterface
     */
    private $paymentFeeRepository;

    public function __construct(
        PaymentFeeRepositoryInterface $paymentFeeRepository,
        array $data = []
    ) {
        parent::__construct($data);
        $this->paymentFeeRepository = $paymentFeeRepository;
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $creditmemo->getOrder();

            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeRepository->getByQuoteId($order->getQuoteId());

            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $paymentFee->getAmount());
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $paymentFee->getBaseAmount());
        } catch (NoSuchEntityException $exception) {
            return $this;
        }

        return $this;
    }
}
