<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class JsonConfig extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_WYOMIND_CONFIG = 'wyomind_elasticsearchcore/configuration';

    /**
     * @var \Magento\Config\Model\ResourceModel\Config|null
     */
    protected $_config = null;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Index\MappingBuilder|null
     */
    protected $_mappingBuilder = null;

    /**
     * @var \Magento\Eav\Model\AttributeRepository|null
     */
    protected $_attributeRepository = null;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    protected $_searchCriteria = null;

    /**
     * @var \Magento\Swatches\Helper\Data|null
     */
    protected $_swatchesHelper = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory|null
     */
    protected $_categoryFactory = null;

    /**
     * @var IndexerFactory|null
     */
    protected $_indexerHelperFactory = null;

    protected $_storeManager = null;

    /**
     * JsonConfig constructor.
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Swatches\Helper\Data $swatchesHelper
     * @param IndexerFactory $indexerHelperFactory
     * @param \Wyomind\ElasticsearchCore\Model\Index\MappingBuilder $mappingBuilder
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Config\Model\ResourceModel\Config $config,
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Swatches\Helper\Data $swatchesHelper,
        IndexerFactory $indexerHelperFactory,
        \Wyomind\ElasticsearchCore\Model\Index\MappingBuilder $mappingBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_config = $config;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_mappingBuilder = $mappingBuilder;
        $this->_attributeRepository = $attributeRepository;
        $this->_searchCriteria = $searchCriteria;
        $this->_swatchesHelper = $swatchesHelper;
        $this->_categoryFactory = $categoryFactory;
        $this->_indexerHelperFactory = $indexerHelperFactory;
        $this->_storeManager = $storeManager;
    }

    /**
     * @param string $storeCode
     * @param array $parameters
     * @throws \Magento\Framework\Exception\InputException
     */
    public function saveConfig($storeCode, $parameters = [])
    {
        $config = $this->getConfig($storeCode, $parameters);
        (new Autocomplete\Config($storeCode))->setData($config);
    }

    /**
     * @param string $storeId
     * @param array $parameters
     * @return array
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getConfig($storeId, $parameters = [])
    {
        $config = $this->getClientConfig($storeId);

        if ($config === null) {
            $storeId = 0;
            $config = $this->getClientConfig($storeId);
        }

        $stores = $this->_storeManager->getStores(true, false);
        foreach($stores as $store){
            if($store->getCode() === $storeId){
                $storeId = $store->getId();
                break;
            }
        }

        foreach ($parameters as $name => $value) {
            if (array_key_exists($name, $config)) {
                $config[$name] = $value;
            }
        }
        $config['types'] = [];

        $types = &$config['types'];
        $indexers = $this->_indexerHelperFactory->create()->getAllIndexers();
        foreach ($indexers as $code => $type) {
            $types[$code] = $this->getValue('wyomind_elasticsearchcore/types/' . $code, $storeId);
            $types[$code]['index_properties'] = $type->getProperties($storeId, true);
            $types[$code]['search_fields'] = $type->getSearchFields($storeId, true, $config['compatibility']);
        }

        // categories tree
        $collection = $this->_categoryFactory->create();
        $collection->addNameToResult()
            ->addIsActiveFilter()
            ->setLoadProductCount(false)
            ->addOrderField('level')
            ->setStore($storeId);
        $tree = [];
        foreach ($collection as $cat) {
            if ($cat->getId() > 2) {
                if ($cat->getParentId() != 2 && isset($tree[$cat->getParentId()])) {
                    $tree[$cat->getId()] = ['label' => $cat->getName(), 'level' => $cat->getLevel(), 'path' => $cat->getPath() . '/'];
                    $tree[$cat->getId()]['parent'] = $cat->getParentId();
                } elseif ($cat->getParentId() == 2) {
                    $tree[$cat->getId()] = ['label' => $cat->getName(), 'level' => $cat->getLevel(), 'path' => $cat->getPath() . '/'];
                }
            }
        }
        $config['categories'] = $tree;

        // filterable attributes information
        $attributes = $this->_attributeRepository->getList(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE, $this->_searchCriteria);

        $optionData = [];
        foreach ($attributes->getItems() as $attribute) {
            $ignoreBackendModel = ["MageWorx\ShippingRules\Model\Attribute\Backend\AvailableShippingMethods"];
            if (in_array($attribute->getBackendModel(), $ignoreBackendModel)) {
                continue;
            }

            $attribute->setStoreId($storeId);
            $options = $attribute->getSource()->getAllOptions();
            $label = "";
            $labels = $attribute->getFrontendLabels();
            if ($labels !== null) {
                foreach ($labels as $l) {
                    if ($l->getStoreId() == $storeId) {
                        $label = $l->getLabel();
                        break;
                    }
                }
            }
            if (empty($label)) {
                $label = $attribute->getFrontendLabel();
            }
            if (!empty($options) && $label !== null) {
                $optionData[$attribute->getAttributeCode()]['id'] = $attribute->getAttributeId();
                $optionData[$attribute->getAttributeCode()]['label'] = $label;
                $optionData[$attribute->getAttributeCode()]['visualSwatch'] = $this->_swatchesHelper->isVisualSwatch($attribute);
                $optionData[$attribute->getAttributeCode()]['textSwatch'] = $this->_swatchesHelper->isTextSwatch($attribute);
                if ($this->_swatchesHelper->isVisualSwatch($attribute) || $this->_swatchesHelper->isTextSwatch($attribute)) {
                    $valueIds = [];
                    foreach ($options as $option) {
                        if (!empty(trim($option['value']))) {
                            $valueIds[] = $option['value'];
                            $optionData[$attribute->getAttributeCode()][$option['value']] = ['label' => $option['label']];
                        }
                    }
                    $images = $this->_swatchesHelper->getSwatchesByOptionsId($valueIds);
                    foreach ($images as $image) {
                        $optionData[$attribute->getAttributeCode()][$image['option_id']]['options'] = [
                            'type' => $image['type'],
                            'swatch' => $image['value']
                        ];
                    }
                } else {
                    foreach ($options as $option) {
                        if (trim($option['label']) != '') {
                            $optionData[$attribute->getAttributeCode()][$option['value']] = ['label' => $option['label']];
                            $optionData[$attribute->getAttributeCode()][$option['value']]['options'] = ['type' => -1, 'swatch' => ''];
                        }
                    }
                }
            }
        }

        $config['swatches'] = $optionData;

        $config = array_merge($config, $this->getValue('wyomind_elasticsearchcore/debug'));

        return $config;
    }

    public function getClientConfig($store = null)
    {
        return $this->getValue(self::XML_PATH_WYOMIND_CONFIG, $store);
    }

    /**
     * @param string $path
     * @param null $store
     * @return mixed
     */
    public function getValue($path, $store = null)
    {
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORES, $store);
    }
}