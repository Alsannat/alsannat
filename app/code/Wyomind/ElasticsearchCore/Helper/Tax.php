<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Tax helper
 */
class Tax extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory
     */
    protected $taxClassCollectionFactory;

    /**
     * @var \Magento\Tax\Model\TaxCalculation
     */
    protected $taxCalculation;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * Tax constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory $taxClassCollectionFactory
     * @param \Magento\Tax\Model\TaxCalculation $taxCalculation
     * @param \Magento\Tax\Model\Config $taxConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory $taxClassCollectionFactory,
        \Magento\Tax\Model\TaxCalculation $taxCalculation,
        \Magento\Tax\Model\Config $taxConfig
    )
    {
        $this->taxClassCollectionFactory = $taxClassCollectionFactory;
        $this->taxCalculation = $taxCalculation;
        $this->taxConfig = $taxConfig;
        parent::__construct($context);
    }

    /**
     * Get all product tax classes
    /**
     * @return \Magento\Tax\Model\ResourceModel\TaxClass\Collection
     */
    protected function getProductTaxClasses()
    {
        return $this->taxClassCollectionFactory->create()->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT);
    }

    /**
     * Get tax rates
     * @param mixed $store
     * @return array
     */
    public function getRates($store = null)
    {
        $rates = [];
        foreach ($this->getProductTaxClasses() as $taxClass) {
            /** @var TaxClass $taxClass */
            $rates[$taxClass->getId()] = $this->taxCalculation->getCalculatedRate(
                $taxClass->getId(),
                null,
                $store
            );
        }

        return $rates;
    }

    /**
     * Should we convert prices
     * @param mixed $store
     * @return bool
     */
    public function needPriceConversion($store = null)
    {
        $priceIncludesTax = $this->priceIncludesTax($store);
        $priceDisplayType = $this->taxConfig->getPriceDisplayType($store);

        if ($priceIncludesTax) {
            return $priceDisplayType == \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX;
        }

        return $priceDisplayType == \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX
            || $priceDisplayType == \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Is prices include tax?
     * @param mixed $store
     * @return bool
     */
    public function priceIncludesTax($store = null)
    {
        return $this->taxConfig->priceIncludesTax($store);
    }
}