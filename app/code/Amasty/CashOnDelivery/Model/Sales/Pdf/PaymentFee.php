<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Sales\Pdf;

use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Calculation;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory;

class PaymentFee extends DefaultTotal
{
    /**
     * @var PaymentFeeRepositoryInterface
     */
    private $paymentFeeRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Data $taxHelper,
        Calculation $taxCalculation,
        CollectionFactory $ordersFactory,
        PaymentFeeRepositoryInterface $paymentFeeRepository,
        OrderRepositoryInterface $orderRepository,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
        $this->paymentFeeRepository = $paymentFeeRepository;
        $this->orderRepository = $orderRepository;
        $this->configProvider = $configProvider;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        try {
            /** @var \Magento\Sales\Model\Order $order */
            $orderId = $this->getSource()->getOrder()->getId();
            $order = $this->orderRepository->get($orderId);

            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeRepository->getByQuoteId($order->getQuoteId());

            return $paymentFee->getAmount();
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->configProvider->getPaymentFeeLabel();
    }
}
