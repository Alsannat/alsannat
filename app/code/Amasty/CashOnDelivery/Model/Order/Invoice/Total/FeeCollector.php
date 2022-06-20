<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Order\Invoice\Total;

use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

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
     * @param Invoice $invoice
     *
     * @return $this
     */
    public function collect(Invoice $invoice)
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $order = $invoice->getOrder();

            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeRepository->getByQuoteId($order->getQuoteId());

            $invoice->setGrandTotal($invoice->getGrandTotal() + $paymentFee->getAmount());
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $paymentFee->getBaseAmount());
        } catch (NoSuchEntityException $exception) {
            return $this;
        }

        return $this;
    }
}
