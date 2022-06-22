<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Api;

/**
 * @api
 */
interface PaymentFeeRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface $paymentFee
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     */
    public function save(\Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface $paymentFee);

    /**
     * Get by id
     *
     * @param int $entityId
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Get by id
     *
     * @param int $quoteId
     *
     * @return \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByQuoteId($quoteId);

    /**
     * Delete
     *
     * @param \Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface $paymentFee
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface $paymentFee);

    /**
     * Delete by id
     *
     * @param int $entityId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
