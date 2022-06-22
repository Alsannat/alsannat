<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Block\Sales\Order;

use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template\Context;

class PaymentFee extends AbstractBlock
{
    /**
     * @var PaymentFeeRepositoryInterface
     */
    private $paymentFeeRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Context $context,
        PaymentFeeRepositoryInterface $paymentFeeRepository,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->paymentFeeRepository = $paymentFeeRepository;
        $this->configProvider = $configProvider;
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        /** @var \Magento\Sales\Block\Adminhtml\Order\Totals $parent */
        $parent = $this->getParentBlock();

        if (!$parent || !method_exists($parent, 'getOrder')) {
            return $this;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $parent->getOrder();

        if (!($order instanceof \Magento\Sales\Api\Data\OrderInterface)) {
            return $this;
        }

        $quoteId = $order->getQuoteId();

        try {
            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeRepository->getByQuoteId($quoteId);
        } catch (NoSuchEntityException $exception) {
            return $this;
        }

        if ($paymentFee->getAmount()) {
            $total = new \Magento\Framework\DataObject(
                [
                    'code' => $this->getNameInLayout(),
                    'label' => $this->configProvider->getPaymentFeeLabel(),
                    'value' => +$paymentFee->getAmount(),
                    'base_value' => +$paymentFee->getBaseAmount()
                ]
            );

            $parent->addTotalBefore($total, 'grand_total');
        }

        return $this;
    }
}
