<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Repository;

use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;
use Amasty\CashOnDelivery\Api\PaymentFeeRepositoryInterface;
use Amasty\CashOnDelivery\Model\PaymentFeeFactory;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee\CollectionFactory;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee\Collection;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Ui\Api\Data\BookmarkSearchResultsInterfaceFactory;
use Magento\Framework\Api\SortOrder;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentFeeRepository implements PaymentFeeRepositoryInterface
{
    /**
     * @var BookmarkSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var PaymentFeeFactory
     */
    private $paymentFeeFactory;

    /**
     * @var PaymentFeeResource
     */
    private $paymentFeeResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $paymentFees;

    /**
     * @var CollectionFactory
     */
    private $paymentFeeCollectionFactory;

    public function __construct(
        BookmarkSearchResultsInterfaceFactory $searchResultsFactory,
        PaymentFeeFactory $paymentFeeFactory,
        PaymentFeeResource $paymentFeeResource,
        CollectionFactory $paymentFeeCollectionFactory
    ) {
        $this->searchResultsFactory = $searchResultsFactory;
        $this->paymentFeeFactory = $paymentFeeFactory;
        $this->paymentFeeResource = $paymentFeeResource;
        $this->paymentFeeCollectionFactory = $paymentFeeCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(PaymentFeeInterface $paymentFee)
    {
        try {
            if ($paymentFee->getEntityId()) {
                $paymentFee = $this->getById($paymentFee->getEntityId())->addData($paymentFee->getData());
            }
            $this->paymentFeeResource->save($paymentFee);
            unset($this->paymentFees[$paymentFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($paymentFee->getEntityId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save paymentFee with ID %1. Error: %2',
                        [$paymentFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new paymentFee. Error: %1', $e->getMessage()));
        }

        return $paymentFee;
    }

    /**
     * @inheritdoc
     */
    public function getById($entityId)
    {
        if (!isset($this->paymentFees[$entityId])) {
            /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
            $paymentFee = $this->paymentFeeFactory->create();
            $this->paymentFeeResource->load($paymentFee, $entityId);
            if (!$paymentFee->getEntityId()) {
                throw new NoSuchEntityException(__('PaymentFee with specified ID "%1" not found.', $entityId));
            }
            $this->paymentFees[$entityId] = $paymentFee;
        }

        return $this->paymentFees[$entityId];
    }

    /**
     * @inheritdoc
     */
    public function getByQuoteId($quoteId)
    {
        /** @var \Amasty\CashOnDelivery\Model\PaymentFee $paymentFee */
        $paymentFee = $this->paymentFeeFactory->create();
        $this->paymentFeeResource->load($paymentFee, $quoteId, PaymentFeeInterface::QUOTE_ID);
        if (!$paymentFee->getEntityId()) {
            throw new NoSuchEntityException(__('PaymentFee with specified ID "%1" not found.', $quoteId));
        }

        return $paymentFee;
    }

    /**
     * @inheritdoc
     */
    public function delete(PaymentFeeInterface $paymentFee)
    {
        try {
            $this->paymentFeeResource->delete($paymentFee);
            unset($this->paymentFees[$paymentFee->getEntityId()]);
        } catch (\Exception $e) {
            if ($paymentFee->getEntityId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove paymentFee with ID %1. Error: %2',
                        [$paymentFee->getEntityId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove paymentFee. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($entityId)
    {
        $paymentFeeModel = $this->getById($entityId);
        $this->delete($paymentFeeModel);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee\Collection $paymentFeeCollection */
        $paymentFeeCollection = $this->paymentFeeCollectionFactory->create();
        
        // Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $paymentFeeCollection);
        }
        
        $searchResults->setTotalCount($paymentFeeCollection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        
        if ($sortOrders) {
            $this->addOrderToCollection($sortOrders, $paymentFeeCollection);
        }
        
        $paymentFeeCollection->setCurPage($searchCriteria->getCurrentPage());
        $paymentFeeCollection->setPageSize($searchCriteria->getPageSize());
        
        $paymentFees = [];
        /** @var PaymentFeeInterface $paymentFee */
        foreach ($paymentFeeCollection->getItems() as $paymentFee) {
            $paymentFees[] = $this->getById($paymentFee->getEntityId());
        }
        
        $searchResults->setItems($paymentFees);

        return $searchResults;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection  $paymentFeeCollection
     *
     * @return void
     */
    private function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $paymentFeeCollection)
    {
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ?: 'eq';
            $paymentFeeCollection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
        }
    }

    /**
    * Helper function that adds a SortOrder to the collection.
    *
    * @param SortOrder[] $sortOrders
    * @param Collection  $paymentFeeCollection
    *
    * @return void
    */
    private function addOrderToCollection($sortOrders, Collection $paymentFeeCollection)
    {
        /** @var SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $paymentFeeCollection->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_DESC) ? SortOrder::SORT_DESC : SortOrder::SORT_ASC
            );
        }
    }
}
