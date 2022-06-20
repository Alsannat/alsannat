<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Quote;

use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Amasty\CashOnDelivery\Model\ConfigProvider;
use Amasty\CashOnDelivery\Model\PaymentFeeFactory;
use Amasty\CashOnDelivery\Model\PaymentValidator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Psr\Log\LoggerInterface;
use Amasty\CashOnDelivery\Model\Config\Source\PaymentFeeTypes;
use Amasty\CashOnDelivery\Model\Config\Source\CalculateBasedOn;

class FeeCollector extends AbstractTotal
{
    const HUNDRED_PERCENT = 100;

    /**
     * @var PaymentValidator
     */
    private $paymentValidator;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var PaymentFeeRepositoryInterface
     */
    private $paymentFeeRepository;

    /**
     * @var PaymentFeeFactory
     */
    private $paymentFeeFactory;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        PaymentValidator $paymentValidator,
        ConfigProvider $configProvider,
        PaymentFeeRepositoryInterface $paymentFeeRepository,
        PaymentFeeFactory $paymentFeeFactory,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger
    ) {
        $this->paymentValidator = $paymentValidator;
        $this->configProvider = $configProvider;
        $this->paymentFeeRepository = $paymentFeeRepository;
        $this->paymentFeeFactory = $paymentFeeFactory;
        $this->priceCurrency = $priceCurrency;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        try {
            $paymentFeeData = [
                'quote_id' => $quote->getId(),
                'base_amount' => 0,
                'amount' => 0,
            ];

            $total->setBaseTotalAmount($this->getCode(), 0);
            $total->setTotalAmount($this->getCode(), 0);

            if ($total->getBaseSubtotal()) {
                try {
                    /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
                    $paymentFee = $this->paymentFeeRepository->getByQuoteId($quote->getId());
                } catch (NoSuchEntityException $exception) {
                    $paymentFee = $this->paymentFeeFactory->create();
                }

                if ($this->paymentValidator->validatePaymentFee($quote)) {
                    $paymentFeeData['base_amount'] = $this->getPaymentFee($total);
                    $paymentFeeData['amount'] = $this->priceCurrency->convert(
                        $paymentFeeData['base_amount'],
                        $quote->getStoreId()
                    );
                    $total->setBaseTotalAmount($this->getCode(), $paymentFeeData['base_amount']);
                    $total->setTotalAmount($this->getCode(), $paymentFeeData['amount']);
                }

                if ($paymentFee->getAmount() != $paymentFeeData['amount']) {
                    $paymentFee->addData($paymentFeeData);
                    $this->paymentFeeRepository->save($paymentFee);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->critical($exception->getMessage());
        }

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Total $total)
    {
        try {
            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeRepository->getByQuoteId($quote->getId());
        } catch (NoSuchEntityException $exception) {
            return null;
        }

        if ($paymentFee && $paymentFee->getAmount()) {
            return [
                'code' => $this->getCode(),
                'title' => __($this->getLabel()),
                'value' => $paymentFee->getAmount()
            ];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->configProvider->getPaymentFeeLabel();
    }

    /**
     * @param Total $total
     *
     * @return float|int
     */
    private function getPaymentFee(Total $total)
    {
        $fee = $this->configProvider->getPaymentFee();
        $feefree = $this->configProvider->getPaymentFeeFree();

        if ($this->configProvider->getPaymentFeeType() == PaymentFeeTypes::PERCENT) {
            if ($this->configProvider->getCalculateBasedOn() == CalculateBasedOn::EXCLUDING_TAX) {
                $fee = $total->getBaseSubtotal() / self::HUNDRED_PERCENT * $fee;
            } elseif ($this->configProvider->getCalculateBasedOn() == CalculateBasedOn::INCLUDING_TAX) {
                $fee = $total->getBaseSubtotalInclTax() / self::HUNDRED_PERCENT * $fee;
            }
        }

        if ($feefree) {
            if ($total->getBaseSubtotal() > $feefree) {
                $fee = 0;
            }
        }
        return $fee;
    }
}
