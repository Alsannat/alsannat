<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Feed
 */


namespace Amasty\Feed\Model\Export\RowCustomizer;

use Amasty\Feed\Model\Export\Product;
use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;

/**
 * Class Effectivedate
 */
class Effectivedate implements RowCustomizerInterface
{
    const DS = DIRECTORY_SEPARATOR;

    const START_UNIX_DATE = '1978-01-01T00:00';

    const END_UNIX_DATE = '2038-01-01T00:00';

    const SALE_PRICE_EFFECITVEDATE_INDEX = 'sale_price_effective_date';

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $timezone;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule
     */
    private $ruleResource;

    /**
     * @var array
     */
    protected $effectiveDates = [];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\CatalogRule\Model\ResourceModel\Rule $ruleResource
    ) {
        $this->storeManager = $storeManager;
        $this->timezone = $timezone;
        $this->productRepository = $productRepository;
        $this->ruleResource = $ruleResource;
    }

    /**
     * Init array of effective date
     *
     * @inheritdoc
     */
    public function prepareData($collection, $productIds)
    {
        foreach ($collection as &$item) {
            $specialFromDate = false;
            $specialToDate = false;

            $currentDate = (new \DateTime('now'))->getTimestamp();
            $websiteId = $this->storeManager->getStore($collection->getStoreId())->getWebsiteId();
            $rulesData = $this->ruleResource->getRulesFromProduct(
                $currentDate,
                $websiteId,
                \Magento\Customer\Model\GroupManagement::NOT_LOGGED_IN_ID,
                $item->getId()
            );

            if ($rulesData) {
                $rule = current($rulesData);
                $specialFromDate = $rule['from_time'] ? date('Y-m-d', $rule['from_time']) : false;
                $specialToDate = $rule['to_time'] ? date('Y-m-d', $rule['to_time']) : false;
            }

            if (!$specialFromDate && !$specialToDate && $item->getSpecialPrice()) {
                $product = $this->productRepository->get($item->getSku());
                $specialFromDate = $product->getSpecialFromDate();
                $specialToDate = $product->getSpecialToDate();
            }

            if ($specialFromDate || $specialToDate) {
                $id = $item->getId();
                $this->effectiveDates[$id] = $this->getSpecialEffectiveDate($specialFromDate, $specialToDate);
            }
        }
    }

    /**
     * Init array of effective date
     *
     * @inheritdoc
     */
    public function addData($dataRow, $productId)
    {
        $customData = &$dataRow['amasty_custom_data'];

        if (isset($this->effectiveDates[$productId])) {
            $customData[Product::PREFIX_OTHER_ATTRIBUTES] = [
                self::SALE_PRICE_EFFECITVEDATE_INDEX => $this->effectiveDates[$productId]
            ];
        } else {
            $customData[Product::PREFIX_OTHER_ATTRIBUTES] = [
                self::SALE_PRICE_EFFECITVEDATE_INDEX => ""
            ];
        }

        return $dataRow;
    }

    /**
     * Columns are added to header
     *
     * @inheritdoc
     */
    public function addHeaderColumns($columns)
    {
        return $columns;
    }

    /**
     * Get number of additional rows
     *
     * @inheritdoc
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        return $additionalRowsCount;
    }

    /**
     * Get special effective date
     *
     * @param string $specialFromDate
     * @param string $specialToDate
     *
     * @return string
     */
    private function getSpecialEffectiveDate($specialFromDate, $specialToDate)
    {
        return $this->getSpecialFromDate($specialFromDate) . self::DS . $this->getSpecialToDate($specialToDate);
    }

    /**
     * Get first part of effective date
     *
     * @param string $specialFromDate
     *
     * @return string
     */
    private function getSpecialFromDate($specialFromDate = null)
    {
        $timeZoneValue = $this->timezone->getConfigTimezone();
        $timeZone = new \DateTimeZone($timeZoneValue);
        $dateValue = new \DateTime(self::START_UNIX_DATE, $timeZone);

        if ($specialFromDate) {
            $dateValue = new \DateTime($specialFromDate);
        }

        return $dateValue->format('Y-m-d');
    }

    /**
     * Get second part of effective date
     *
     * @param string $specialToDate
     *
     * @return string
     */
    private function getSpecialToDate($specialToDate = null)
    {
        $timeZoneValue = $this->timezone->getConfigTimezone();
        $timeZone = new \DateTimeZone($timeZoneValue);
        $dateValue = new \DateTime(self::END_UNIX_DATE);

        if ($specialToDate) {
            $dateValue = new \DateTime($specialToDate, $timeZone);
        }

        return $dateValue->format('Y-m-d');
    }
}
