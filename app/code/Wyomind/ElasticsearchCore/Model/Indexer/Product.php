<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Indexer;

class Product extends AbstractIndexer
{
    const CLASS_PATH = '\Wyomind\ElasticsearchCore\Model\Indexer\Product';
    const SKU_SUGGESTER = 'sku_suggester';
    const NAME_SUGGESTER = 'name_suggester';
    const PRODUCT_PARENT_IDS = 'parent_ids';
    const PRODUCT_CATEGORIES = 'categories';
    const PRODUCT_PRICES = 'prices';
    const PRODUCT_URL = 'url';
    const PRODUCT_SHORTEST_URL = 'shortest_url';
    const PRODUCT_LONGEST_URL = 'longest_url';
    const PRODUCT_CATEGORIES_PARENT_ID = 'categories_parent_ids';

    /**
     * @var string
     */
    public $type = 'product';

    /**
     * @var string
     */
    public $name = 'Products';

    /**
     * @var string
     */
    public $comment = 'Products main indexer';

    /**
     * @var int
     */
    protected $_productsChunkSize = 500;

    /**
     * @var int
     */
    protected $_attributesChunkSize = 25;

    /**
     * @var string|null
     */
    protected $_excludedCategory = null;

    /**
     * @var array
     */
    protected $_eventExcludedProducts = [];

    /**
     * Searchable attributes cache
     * @var \Magento\Eav\Model\Entity\Attribute[]
     */
    protected $_searchableAttributes = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory = null;

    /**
     * @var \Magento\Eav\Model\EntityFactory
     */
    protected $_entityFactory = null;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource = null;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $_reviewFactory = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Category
     */
    protected $_categoryHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\ToReindexFactory
     */
    protected $_toReindexModelFactory = null;

    /**
     * Product constructor.
     * @param Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Eav\Model\EntityFactory $entityFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Wyomind\ElasticsearchCore\Helper\Category $categoryHelper
     * @param \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory
     */
    public function __construct(
        Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\EntityFactory $entityFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Wyomind\ElasticsearchCore\Helper\Category $categoryHelper,
        \Wyomind\ElasticsearchCore\Model\ToReindexFactory $toReindexModelFactory
    )
    {
        parent::__construct($context);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_entityFactory = $entityFactory;
        $this->_resource = $resource;
        $this->_reviewFactory = $reviewFactory;
        $this->_categoryHelper = $categoryHelper;
        $this->_toReindexModelFactory = $toReindexModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function export($storeId, $ids = [])
    {
        $this->handleLog('');
        $this->handleLog('<comment>' . __('Indexing products for store id: ') . $storeId . '</comment>');

        $this->_eventManager->dispatch('wyomind_elasticsearchcore_product_export_before', ['store_id' => $storeId, 'ids' => $ids]);
        try {
            set_time_limit(0); // export might be a bit slow

            /** @var \Magento\Store\Model\Store $store */
            $store = $this->_storeManager->getStore($storeId);
            $categoryNames = $this->_categoryHelper->getCategoriesWithPathNames($storeId);
            $defaultGroupId = \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;

            $productEntity = $this->_entityFactory->create()->setType(\Magento\Catalog\Model\Product::ENTITY);
            $attributesByTable = $productEntity->loadAllAttributes()->getAttributesByTable();
            $mainTable = $this->_resource->getTableName('catalog_product_entity');
            $connection = $this->_resource->getConnection();
            $connection->query('SET SESSION group_concat_max_len = 10000;');

            $ccpiTable = $this->_resource->getTableName('catalog_category_product');
            if (class_exists('\Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer')
                && $tableMaintainer = $this->_objectManager->get('\Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer')) {
                $indexTableName = $tableMaintainer->getMainTable($storeId);
                $ccpiTable = $this->_resource->getTableName($indexTableName);
            }

            $rowId = $this->moduleIsEnabled('Magento_Enterprise') ? 'row_id' : 'entity_id';

            // Export all products of current store
            $select = $connection->select()->from(['e' => $mainTable], 'entity_id');

            if (false === empty($ids)) {
                $select->where('e.entity_id IN (?)', $ids);
            }

            // Filter products that are enabled for current store website
            $select->join(
                ['product_website' => $this->_resource->getTableName('catalog_product_website')],
                'product_website.product_id = e.entity_id AND ' . $connection->quoteInto('product_website.website_id = ?',
                    $store->getWebsiteId()),
                []
            );


            // MAGENTO 2.3
            if ($this->_stockResolver !== null) { // _stockResolver only := null when using Magento 2.3

                $websiteCode = $this->_storeManager->getWebsite($store->getWebsiteId())->getCode();

                $stock = $this->_stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
                $stockId = (int)$stock->getStockId();

                $subSelect = $this->_selectBuilder->execute($stockId);

                $select->joinLeft(
                    ['stock_index' => $subSelect],
                    'e.sku = stock_index.sku',
                    []
                );


            }

            if (!$this->_configHelper->isIndexOutOfStockProducts($store)) {

                if ($this->_stockResolver !== null) { // Magento 2.3
                    $select->where('stock_index.' . \Magento\InventoryIndexer\Indexer\IndexStructure::IS_SALABLE . ' = ? or e.type_id in ("configurable","bundle","grouped")', 1);

                } else { // Magento < 2.3
                    $manageStock = $this->_configHelper->isManageStock($store);
                    $condArr = [
                        'stock.use_config_manage_stock=0 AND stock.manage_stock=1 AND stock.is_in_stock=1',
                        'stock.use_config_manage_stock=0 AND stock.manage_stock=0',
                    ];

                    if ($manageStock) {
                        $condArr[] = 'stock.use_config_manage_stock=1 AND stock.is_in_stock=1';
                    } else {
                        $condArr[] = 'stock.use_config_manage_stock=1';
                    }

                    $cond = '((' . implode(') OR (', $condArr) . '))';
                    $select->join(
                        ['stock' => $this->_resource->getTableName('cataloginventory_stock_item')],
                        '(stock.product_id = e.entity_id) AND ' . $cond,
                        []
                    );
                }
            }

            // ignore products?
            $attributeCode = "elasticsearchcore_ignore";
            $attributeId = $productEntity->getAttribute($attributeCode)->getId();
            $alias1 = $attributeCode . '_default';
            $select->joinLeft(
                [$alias1 => $this->_resource->getTableName('catalog_product_entity_int')],
                $alias1 . '.attribute_id = ' . $attributeId . ' AND ' . $alias1 . '.' . $rowId . ' = e.' . $rowId . ' AND ' . $alias1 . '.store_id = 0',
                []
            );
            $alias2 = $attributeCode . '_store';
            $valueExpr = $connection->getCheckSql($alias2 . '.value IS NULL', $alias1 . '.value', $alias2 . '.value');
            $select->joinLeft(
                [$alias2 => $this->_resource->getTableName('catalog_product_entity_int')],
                $alias2 . '.attribute_id = ' . $attributeId . ' AND ' . $alias2 . '.' . $rowId . ' = e.' . $rowId . ' AND ' . $alias2 . '.store_id = ' . $store->getId(),
                []
            );
            $select->where("$valueExpr != 1 OR $valueExpr is null");

            // Handle enabled products
            $attributeId = $productEntity->getAttribute('status')->getId();
            if ($attributeId) {
                $select->join(
                    ['status' => $this->_resource->getTableName('catalog_product_entity_int')],
                    'status.attribute_id = ' . $attributeId . ' AND status.' . $rowId . ' = e.' . $rowId,
                    []
                );
                $enabled = \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
                $select->where('status.value = ?', $enabled);
                $select->where('status.store_id IN (?)', [0, $storeId]);
            }

            // Fetch entity ids that match
            $ids = $connection->fetchCol($select);
            $ids = array_unique($ids);

            // Handle N products max at a time
            $allEntityIds = array_chunk($ids, $this->_productsChunkSize);

            $nbChunks = count($allEntityIds);
            $chunkCounter = 1;

            $this->handleLog('<info>' . count($ids) . ' products found</info>');
            $this->handleLog('<info>Split into ' . $nbChunks . ' chunks for better performance</info>');

            foreach ($allEntityIds as $i => $entityIds) {
                $this->handleLog('' . ($chunkCounter++) . '/' . $nbChunks);

                // Loop through products
                $products = [];
                $attrOptionLabels = [];
                foreach ($attributesByTable as $table => $allAttributes) {
                    $allAttributes = array_chunk($allAttributes, $this->_attributesChunkSize);
                    foreach ($allAttributes as $attributes) {
                        $select = $connection->select()->from(['e' => $mainTable], ['id' => 'entity_id', 'sku', 'type_id']);

                        foreach ($attributes as $attribute) {
                            /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
                            if (!$this->_attributeHelper->isAttributeIndexable($attribute) && $attribute->getAttributeCode() != "product_weight") {
                                continue;
                            }

                            $attributeId = $attribute->getAttributeId();
                            $attributeCode = $attribute->getAttributeCode();

                            if (!isset($attrOptionLabels[$attributeCode]) && $this->_attributeHelper->isAttributeUsingOptions($attribute)) {
                                $options = $attribute->setStoreId($storeId)->getSource()->getAllOptions();
                                foreach ($options as $option) {
                                    if (!$option['value']) {
                                        continue;
                                    }
                                    $attrOptionLabels[$attributeCode][$option['value']] = $option['label'];
                                }
                            }

                            $alias1 = $attributeCode . '_default';
                            $select->joinLeft(
                                [$alias1 => $this->_resource->getTableName($table)],
                                $alias1 . '.attribute_id = ' . $attributeId . ' AND ' . $alias1 . '.' . $rowId . ' = e.' . $rowId . ' AND ' . $alias1 . '.store_id = 0',
                                []
                            );
                            $alias2 = $attributeCode . '_store';
                            $valueExpr = $connection->getCheckSql($alias2 . '.value IS NULL', $alias1 . '.value', $alias2 . '.value');
                            $select->joinLeft(
                                [$alias2 => $this->_resource->getTableName($table)],
                                $alias2 . '.attribute_id = ' . $attributeId . ' AND ' . $alias2 . '.' . $rowId . ' = e.' . $rowId . ' AND ' . $alias2 . '.store_id = ' . $store->getId(),
                                [$attributeCode => $valueExpr]
                            );
                        }

                        $select->where('e.entity_id IN (?)', $entityIds);

                        $query = $connection->query($select);
                        while ($row = $query->fetch()) {
                            $row = array_filter($row, 'strlen');
                            $row['id'] = (int)$row['id'];
                            $productId = $row['id'];
                            if (!isset($products[$productId])) {
                                $products[$productId] = [];
                            }
                            foreach ($row as $code => &$value) {
                                if ($code == 'image') {
                                    $attributeData = clone $attributesByTable[$table][$code];
                                    $attributeData->setFrontendInput('media_base_image');
                                    $valueBase = $this->_attributeHelper->formatAttributeValue($attributeData, $value, $store);
                                    $row['base_' . $code] = $valueBase;
                                    $attributeData->setFrontendInput('media_image');
                                }
                                if (isset($attributesByTable[$table][$code])) {
                                    $value = $this->_attributeHelper->formatAttributeValue($attributesByTable[$table][$code], $value, $store);
                                }

                                if (isset($attrOptionLabels[$code])) {
                                    // liste d'ids d'options ?
                                    $matches = [];
                                    $re = "/([0-9]+), ?/";
                                    preg_match_all($re, $value, $matches);
                                    if (!empty($matches[1])) {
                                        $row[$code . '_ids'] = explode(',', $value);
                                        $value = explode(',', $value);
                                    } else {
                                        $matches = [];
                                        $re = "/([0-9]+)?/";
                                        preg_match_all($re, $value, $matches);
                                        if (!empty($matches[1])) {
                                            $row[$code . '_ids'] = $value;
                                            $value = explode(',', $value);
                                        }
                                    }
                                    if (is_array($value)) {
                                        $label = [];
                                        foreach ($value as $val) {
                                            if (isset($attrOptionLabels[$code][$val])) {
                                                $label[] = $attrOptionLabels[$code][$val];
                                            }
                                        }
                                        if (!empty($label)) {
                                            $row[$code] = $label;
                                        }
                                    } elseif (isset($attrOptionLabels[$code][$value])) {
                                        $row[$code] = $attrOptionLabels[$code][$value];
                                    }
                                }
                                if ($code == 'sku') {
                                    $row[$code] = mb_strtolower($row[$code]);
                                    $row[self::SKU_SUGGESTER] = mb_strtolower($row[$code]);
                                }
                                if ($code == 'name') {
                                    $row[self::NAME_SUGGESTER] = mb_strtolower($row[$code]);
                                }
                            }
                            unset($value);

                            $products[$productId] = array_merge($products[$productId], $row);
                        }
                    }
                }

                // categories columns
                $columns = [
                    'product_id' => 'product_id',
                    'category_ids' => new \Zend_Db_Expr(
                        "TRIM(
                            BOTH ',' FROM CONCAT(
                            TRIM(BOTH ',' FROM GROUP_CONCAT(IF(position = 1, category_id, '') SEPARATOR ',')),
                             ',',
                             TRIM(BOTH ',' FROM GROUP_CONCAT(IF(position >= 0, category_id, '') SEPARATOR ','))
                            )
                        )"
                    )

                ];

                // Add parent products in order to retrieve products that have associated products
                if ($this->moduleIsEnabled('Magento_Enterprise')) {
                    $select = $connection->select()
                        ->from($this->_resource->getTableName('catalog_product_relation'), ['child_id'])
                        ->joinLeft(
                            ['cpe' => $this->_resource->getTableName('catalog_product_entity')],
                            'cpe.row_id = parent_id',
                            ['parent_id' => 'cpe.entity_id']
                        )
                        ->where('child_id IN (?)', $entityIds);
                } else {
                    $select = $connection->select()
                        ->from($this->_resource->getTableName('catalog_product_relation'), ['parent_id', 'child_id'])
                        ->where('child_id IN (?)', $entityIds);
                }

                $query = $connection->query($select);

                while ($row = $query->fetch()) {
                    $productId = $row['child_id'];
                    if (!isset($products[$productId][self::PRODUCT_PARENT_IDS])) {
                        $products[$productId][self::PRODUCT_PARENT_IDS] = [];
                    }
                    $products[$productId][self::PRODUCT_PARENT_IDS][] = (int)$row['parent_id'];

                    // retrieve parent categories
                    $parentId = $row['parent_id'];
                    $selectParent = $connection->select()
                        ->from($ccpiTable, $columns)
                        ->where('product_id IN (?)', $parentId)
                        ->where('category_id > 1'); // ignore global root category
                    // fix for Amasty Shopby !
                    if (!$this->moduleIsEnabled('Amasty_Shopby') && !$this->moduleIsEnabled('Amasty_ShopbyBrand')) {
                        $selectParent->where('category_id != ?', $store->getRootCategoryId()); // ignore store root category
                    }

                    $selectParent->group('product_id');

                    $queryParent = $connection->query($selectParent);
                    $rowParent = $queryParent->fetch();
                    if ($rowParent != null) {
                        $products[$productId][self::PRODUCT_CATEGORIES_PARENT_ID] = array_values(array_unique(array_filter(explode(',', $rowParent['category_ids']))));
                    }
                }

                // Add categories
                $columns['cat_pos'] = new \Zend_Db_Expr(
                    "TRIM(
                            BOTH ',' FROM CONCAT(
                            TRIM(BOTH ',' FROM GROUP_CONCAT(IF(position = 1, position, '') SEPARATOR ',')),
                             ',',
                             TRIM(BOTH ',' FROM GROUP_CONCAT(IF(position >= 0, position, '') SEPARATOR ','))
                            )
                        )"
                );
                $select = $connection->select()
                    ->from($ccpiTable, $columns)
                    ->where('product_id IN (?)', $entityIds)
                    ->where('category_id > 1'); // ignore global root category
                // fix for Amasty Shopby !
                if (!$this->moduleIsEnabled('Amasty_Shopby') && !$this->moduleIsEnabled('Amasty_ShopbyBrand')) {
                    $select->where('category_id != ?', $store->getRootCategoryId()); // ignore store root category
                }

                $select->group('product_id');

                $query = $connection->query($select);

                while ($row = $query->fetch()) {
                    $categoryIds = explode(',', $row['category_ids']);
                    $catPos = explode(',', $row['cat_pos']);

                    if (empty($categoryIds)) {
                        continue;
                    }
                    $productId = $row['product_id'];
                    if (!isset($products[$productId][self::PRODUCT_CATEGORIES])) {
                        $products[$productId][self::PRODUCT_CATEGORIES] = [];
                        $products[$productId][self::PRODUCT_CATEGORIES . '_ids'] = [];
                    }

                    $counter = 0;
                    foreach ($categoryIds as $categoryId) {
                        // wyomind_elasticsearchcore_event_reindex_catalog_category_commit_after
                        if (!empty($this->_eventExcludedProducts)) {
                            if (in_array($productId, $this->_eventExcludedProducts) && $this->_excludedCategory === $categoryId) {
                                continue;
                            }
                        }

                        /** @var \Magento\Catalog\Model\Category $category */
                        $category = $categoryNames->getItemById($categoryId);
                        if ($category) {
                            $category->setStoreId($storeId);
                            $catName = $this->_categoryHelper->getCategoryPathName($category);
                            if ($catName != '') {
                                $products[$productId][self::PRODUCT_CATEGORIES][] = $catName;
                            }
                            $products[$productId][self::PRODUCT_CATEGORIES . '_ids'][] = $categoryId;
                            $products[$productId]["cat_pos_" . $categoryId] = $catPos[$counter];
                        }
                        $counter++;
                    }
                    $products[$productId][self::PRODUCT_CATEGORIES] = array_values(array_unique($products[$productId][self::PRODUCT_CATEGORIES]));
                    $products[$productId][self::PRODUCT_CATEGORIES . '_ids'] = array_values(array_unique($products[$productId][self::PRODUCT_CATEGORIES . '_ids']));
                }

                // Add prices
                $least = $connection->getLeastSql(['prices.min_price', 'prices.tier_price']);
                $minimalExpr = $connection->getCheckSql('prices.tier_price IS NOT NULL', $least, 'prices.min_price');
                $cols = ['customer_group_id', 'entity_id', 'price', 'final_price', 'minimal_price' => $minimalExpr, 'min_price', 'max_price', 'tier_price'];
                $select = $connection->select()
                    ->from(['prices' => $this->_resource->getTableName('catalog_product_index_price')], $cols)
                    ->where('prices.entity_id IN (?)', $entityIds)
                    ->where('prices.website_id = ?', $store->getWebsiteId());
                $select->joinLeft(
                    ['price_rule' => $this->_resource->getTableName('catalogrule_product_price')],
                    'price_rule.product_id = prices.entity_id AND '
                    . $connection->quoteInto('price_rule.website_id = ?', $store->getWebsiteId()) .' AND '
                    . 'price_rule.customer_group_id = prices.customer_group_id',
                    ['rule_price']
                );

                $query = $connection->query($select);
                while ($row = $query->fetch()) {

                    $productId = $row['entity_id'];
                    unset($row['entity_id']);

                    // Format prices as floats
                    array_walk($row, array(self::CLASS_PATH, 'convertPrice'));

                    if ($row['final_price'] > $row['rule_price'] && $row['rule_price'] != 0) {
                        $row['final_price'] = $row['rule_price'];
                    }

                    // needed because the best price for configurable product is computed dynamically (!?)
                    try {
                        if (isset($products[$productId]['type_id']) && $products[$productId]['type_id'] == "configurable") {
                            if ($row['final_price'] == 0) {
                                $row['final_price'] = $row['min_price'];
                            }
                            if ($row['price'] == 0) {
                                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                                $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                                $product->setStoreId($storeId);
                                $row['price'] = $product->getPriceInfo()->getPrice("base_price")->getAmount()->getBaseAmount();
                            }
                            if ($row['price'] == 0) {
                                $row['price'] = $row['min_price'];
                            }
                        }

                        if (isset($products[$productId]['type_id']) && $products[$productId]['type_id'] == 'bundle') {
                            $products[$productId]['price'] = $row['min_price'];
                            $row['price'] = $row['min_price'];
                            $row['final_price'] = $row['min_price'];

                        }

                    } catch (\Exception $e) {
                        // to avoid to stop the export if a configurable product don't have child
                    }

                    $customerGroupId = $row['customer_group_id'];
                    unset($row['customer_group_id']);
                    $products[$productId][self::PRODUCT_PRICES . "_" . $customerGroupId] = $row;
                }

                // Add product URL
                $select = $connection->select()
                    ->from($this->_resource->getTableName('url_rewrite'), [
                        'product_id' => 'entity_id',
                        'request_path' => 'group_concat(request_path)',
                        'metadata' => 'group_concat(IFNULL(metadata, ""))'
                    ])
                    ->where('store_id = ?', $storeId)
                    ->where('entity_type = ?', \Magento\UrlRewrite\Controller\Adminhtml\Url\Rewrite::ENTITY_TYPE_PRODUCT)
                    ->where('redirect_type = 0')
                    ->where('entity_id IN (?)', $entityIds)
                    ->group('entity_id');

                $query = $connection->query($select);
                $baseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, $store->isFrontUrlSecure());
                $baseUrl = str_replace(['/magento/', '/n98-magerun2/'], ['/', '/'], $baseUrl);

                while ($row = $query->fetch()) {
                    $productId = $row['product_id'];
                    $metadatas = explode(',', $row['metadata']);
                    $requestPaths = explode(',', $row['request_path']);
                    $nbPaths = count($requestPaths);
                    for ($i = 0; $i < $nbPaths; $i++) {
                        if (!isset($metadatas[$i]) || $metadatas[$i] == '') {
                            $products[$productId][self::PRODUCT_URL] = $baseUrl . $requestPaths[$i];
                            unset($requestPaths[$i]);
                        }
                    }
                    usort($requestPaths, array(self::CLASS_PATH, 'compareRequestPaths'));
                    if (count($requestPaths) > 0) {
                        $products[$productId][self::PRODUCT_SHORTEST_URL] = $baseUrl . $requestPaths[0];
                        $products[$productId][self::PRODUCT_LONGEST_URL] = $baseUrl . array_pop($requestPaths);
                    }
                }

                $configurable = $this->_objectManager->create('\Wyomind\ElasticsearchCore\Model\Product\Type\Configurable');

                foreach ($products as $productId => $data) {
                    // configurable children options
                    if (isset($data['type_id']) && $data['type_id'] == 'configurable') {

                        $product = $this->_objectManager->create('\Magento\Catalog\Model\Product')->load($productId);
                        $product->setStoreId($storeId);

                        $renderer = $this->_objectManager->create('\Wyomind\ElasticsearchCore\Block\Product\Renderer\Configurable');
                        $renderer->setProduct($product);
                        $jsonConfig = json_decode($renderer->getJsonConfig(), true);

                        $products[$productId]['configurable_index'] = json_encode($jsonConfig['index']);
                        $products[$productId]['configurable_images'] = json_encode($jsonConfig['images']);

                        $coll = $configurable->getSalableProducts($product);
                        $usedProducts = array_values($coll->getItems());
                        $usedAttributes = $configurable->getUsedProductAttributes($product);

                        if ($usedAttributes != null) {
                            foreach ($usedAttributes as $attribute) {
                                if (!$this->_attributeHelper->isAttributeIndexable($attribute)) {
                                    continue;
                                }
                                $values = [];
                                $code = $attribute->getAttributeCode();
                                foreach ($usedProducts as $uP) {
                                    $values[] = $uP->getData($code);
                                }
                                if (!empty($values)) {
                                    $products[$productId][$code . '_ids'] = array_values(array_unique($values));
                                    $products[$productId]['configurable_options'][] = $code;
                                }
                            }
                        }
                    }
                }


                if ($this->_configHelper->isIndexOutOfStockProducts($store)) {


                    // MAGENTO 2.3
                    if ($this->_stockResolver !== null) { // _stockResolver only := null when using Magento 2.3

                        $websiteCode = $this->_storeManager->getWebsite($store->getWebsiteId())->getCode();

                        $stock = $this->_stockResolver->execute(\Magento\InventorySalesApi\Api\Data\SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
                        $stockId = (int)$stock->getStockId();

                        $subSelect = $this->_selectBuilder->execute($stockId);

                        $select = $connection->select()->from(['e' => $mainTable], 'entity_id');
                        $select->columns(['is_salable' => 'stock_index.is_salable', 'type_id' => 'e.type_id']);
                        $select->joinInner(
                            ['stock_index' => $subSelect],
                            'e.sku = stock_index.sku',
                            []
                        );
                        //$select->where('stock_index.' . \Magento\InventoryIndexer\Indexer\IndexStructure::IS_SALABLE . ' = ?', 1);
                        $select->where('e.entity_id IN (?)', $entityIds);

                        $query = $connection->query($select);
                        while ($row = $query->fetch()) {
                            $productId = $row['entity_id'];
                            $products[$productId]['quantity_and_stock_status_ids'] =
                                $row[\Magento\InventoryIndexer\Indexer\IndexStructure::IS_SALABLE] == 1 ||
                                in_array($row['type_id'], ["configurable", "bundle", "grouped"]) ? '1' : '0';
                        }

                    } else {

                        $manageStock = $this->_configHelper->isManageStock($store);
                        $condArr = [
                            'stock.use_config_manage_stock=0 AND stock.manage_stock=1 AND stock.is_in_stock=1',
                            'stock.use_config_manage_stock=0 AND stock.manage_stock=0',
                        ];
                        if ($manageStock) {
                            $condArr[] = 'stock.use_config_manage_stock=1 AND stock.is_in_stock=1';
                        } else {
                            $condArr[] = 'stock.use_config_manage_stock=1';
                        }
                        $cond = '((' . implode(') OR (', $condArr) . '))';
                        $select = $connection->select()->from(['e' => $mainTable], 'entity_id');
                        $select->columns(['stock' => $cond]);
                        $select->join(
                            ['product_website' => $this->_resource->getTableName('catalog_product_website')],
                            'product_website.product_id = e.entity_id AND ' . $connection->quoteInto('product_website.website_id = ?', $store->getWebsiteId()),
                            []
                        );
                        $select->join(
                            ['stock' => $this->_resource->getTableName('cataloginventory_stock_item')],
                            '(stock.product_id = e.entity_id)',
                            []
                        );
                        $select->where('entity_id IN (?)', $entityIds);
                        $query = $connection->query($select);

                        while ($row = $query->fetch()) {
                            $productId = $row['entity_id'];
                            $products[$productId]['quantity_and_stock_status_ids'] = $row['stock'] == 1 ? '1' : '0';
                        }
                    }


                }

                // reviews
                $select = $connection->select()
                    ->from(['rating_option_vote' => $this->_resource->getTableName('rating_option_vote')], ["rating_option_vote.entity_pk_value", 'count(*) as vote_count', 'round(avg(rating_option_vote.percent)) as percent_approved'])
                    ->joinInner(["review" => $this->_resource->getTableName('review')], "rating_option_vote.review_id = review.review_id")
                    ->joinInner(["review_detail" => $this->_resource->getTableName('review_detail')], "review_detail.review_id = review.review_id")
                    ->where('rating_option_vote.entity_pk_value IN (?)', $entityIds)
                    ->where('store_id = ?', $storeId)
                    ->where(" review.status_id = 1")
                    ->group("rating_option_vote.entity_pk_value");
                $query = $connection->query($select);
                while ($row = $query->fetch()) {
                    $productId = $row['entity_pk_value'];
                    $products[$productId]['rating'] = $row['percent_approved'];
                    $products[$productId]['review_count'] = $row['vote_count'];
                }

                foreach (array_keys($products) as $key) {
                    if (!isset($products[$key]['product_weight'])) {
                        $products[$key]['product_weight'] = 1;
                    }
                    if (!isset($products[$key]['rating'])) {
                        $products[$key]['rating'] = -1;
                        $products[$key]['review_count'] = 0;
                    }

                    if (!$this->_configHelper->isIndexOutOfStockProducts($store)) {
                        $products[$key]['quantity_and_stock_status_ids'] = 1;
                    }

                    if (isset($products[$key]['visibility'])
                        && $products[$key]['visibility'] > 2
                        && !isset($products[$key][self::PRODUCT_URL])
                    ) {
                        $products[$key][self::PRODUCT_URL] = $baseUrl . 'catalog/product/view/id/' . $key;
                        $products[$key][self::PRODUCT_SHORTEST_URL] = $baseUrl . 'catalog/product/view/id/' . $key;
                        $products[$key][self::PRODUCT_LONGEST_URL] = $baseUrl . 'catalog/product/view/id/' . $key;
                    }
                }

                yield $products;
            }

            $this->handleLog('<info>' . count($ids) . __(' products indexed') . '</info>');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->handleError($e->getMessage());
        }
        $this->_eventManager->dispatch('wyomind_elasticsearchcore_product_export_after', ['store_id' => $storeId, 'ids' => $ids]);
    }

    protected function getSearchableAttributes($store, $backendType = null)
    {
        if (!isset($this->_searchableAttributes[$store])) {
            $atts = $this->_configHelper->getEntitySearchableAttributes('product', $store);
            foreach ($atts as $attributeCode => $attributeInfo) {
                if ($attributeInfo['c'] === '1') {
                    if ($attributeInfo['b'] == 'varchar' || $attributeInfo['b'] == 'text' || $attributeInfo['b'] == 'static') {
                        $attributeInfo['b'] = 'string';
                    }
                    if ($attributeInfo['b'] == 'int') {
                        $attributeInfo['b'] = 'integer';
                    }
                    if ($attributeInfo['b'] == 'decimal') {
                        $attributeInfo['b'] = 'float';
                    }
                    if ($attributeInfo['b'] == 'datetime') {
                        $attributeInfo['b'] = 'date';
                    }
                    $this->_searchableAttributes[$store][$attributeCode] = $attributeInfo;
                }
            }

            $this->_searchableAttributes[$store]['special_from_date'] = ['c' => 1, 'b' => 'date', 'w' => 1];
            $this->_searchableAttributes[$store]['special_to_date'] = ['c' => 1, 'b' => 'date', 'w' => 1];
        }

        if ($backendType !== null) {
            $backendType = (array)$backendType;
            $attributes = [];
            foreach ($this->_searchableAttributes[$store] as $attributeCode => $attributeInfo) {
                if (in_array($attributeInfo['b'], $backendType)) {
                    $attributes[$attributeCode] = $attributeInfo;
                }
            }

            // required attributes:
            $attributes['special_from_date'] = ['c' => 1, 'b' => 'date', 'w' => 1];
            $attributes['special_to_date'] = ['c' => 1, 'b' => 'date', 'w' => 1];

            return $attributes;
        }
        return $this->_searchableAttributes[$store];
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($store = null, $withBoost = false)
    {
        $properties = [];

        $attributes = $this->getSearchableAttributes($store, ['varchar', 'int']);

        foreach ($attributes as $attributeCode => $attributeInfo) {
            // if ($this->_attributeHelper->isAttributeIndexable($attribute)) {
//                $key = $attributeCode;
            $properties[$attributeCode] = $this->getAttributeProperties($attributeCode, $attributeInfo, $store, $withBoost);
            $properties[$attributeCode . '_ids'] = ['type' => 'integer'];
            //  }
        }

        $attributes = $this->getSearchableAttributes($store, ['string']);
        foreach ($attributes as $attributeCode => $attributeInfo) {
            $key = $attributeCode;
            $properties[$key] = $this->getAttributeProperties($attributeCode, $attributeInfo, $store, $withBoost);
        }

        $compatibility = $this->_configHelper->getCompatibility($store);

        if ($compatibility >= 6) {
            $attributes = $this->getSearchableAttributes($store, ['static', 'varchar', 'decimal', 'datetime']);
            foreach ($attributes as $attributeCode => $attributeInfo) {
//                $key = $attribute->getAttributeCode();
                $key = $attributeCode;
                if (/*$this->_attributeHelper->isAttributeIndexable($attribute) && */
                !isset($properties[$attributeCode])) {
//                    $type = $this->getAttributeType($attribute, $compatibility);
                    $type = $attributeInfo['b']; // backend_type
                    if ($type === 'option') {
                        continue;
                    }
                    $properties[$key] = ['type' => $type];

                    //if ((bool)$attribute->getIsSearchable()) {
                    $properties[$key]['copy_to'] = 'all';
                    //}

                    if ($withBoost) {
                        $boost = (int)$attributeInfo['w']; // attribute weight
                        if ($boost > 1) {
                            $properties[$key]['boost'] = $boost;
                        }
                    }

                    if ($key == 'sku') {
                        $properties[$key]['copy_to'] = 'all';
                        $properties[$key]['type'] = 'text';
                        $properties[$key]['fields']['suffix'] = [
                            'type' => 'text',
                            'analyzer' => 'text_suffix',
                            'search_analyzer' => 'std'
                        ];
                        $properties[$key]['fields']['prefix'] = [
                            'type' => 'text',
                            'analyzer' => 'text_prefix',
                            'search_analyzer' => 'std'
                        ];
                        //$properties[$key]['index'] = 'false';
                    }

                    if ($type == 'datetime') {
                        $properties[$key]['format'] = 'date';
                    }
                }
            }
            $properties['product_weight']['type'] = 'integer';

            // Add categories field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_CATEGORIES] = [
                'type' => 'text',
                'copy_to' => 'all',
                'analyzer' => $this->getLanguageAnalyzer($store),
            ];


            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_CATEGORIES . '_ids'] = ['type' => 'integer'];

            // Add parent_ids field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_PARENT_IDS] = [
                'type' => 'integer',
                'store' => 'true',
                'index' => 'false',
            ];

            // Add URL field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_URL] = [
                'type' => 'text',
                'store' => 'true',
                'index' => 'false',
            ];

            // Add name autocompletion
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::NAME_SUGGESTER] = [
                'type' => 'completion',
                'analyzer' => 'std',
                'search_analyzer' => 'std'
            ];
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::SKU_SUGGESTER] = [
                'type' => 'completion',
                'analyzer' => 'std',
                'search_analyzer' => 'std'
            ];

            $properties['all'] = [
                'type' => 'text'
            ];
        } elseif ($compatibility < 6) {
            $attributes = $this->getSearchableAttributes($store, ['static', 'varchar', 'decimal', 'datetime']);
            foreach ($attributes as $attributeCode => $attributeInfo) {
                $key = $attributeCode;
                if (/*$this->_attributeHelper->isAttributeIndexable($attribute) &&*/
                !isset($properties[$key])) {
                    $type = $attributeInfo['b']; // backend_type
                    if ($type === 'option') {
                        continue;
                    }

                    $properties[$key] = [
                        'type' => $type,
                        'include_in_all' => true
                    ];

                    if ($withBoost) {
                        $boost = (int)$attributeInfo['b']; // backend_type
                        if ($boost > 1) {
                            $properties[$key]['boost'] = $boost;
                        }
                    }

                    if ($key == 'sku') {
                        $properties[$key]['include_in_all'] = true;
                        $properties[$key]['type'] = 'string';
                        //$properties[$key]['index'] = 'not_analyzed';
                    }

                    if ($type == 'datetime') {
                        $properties[$key]['format'] = 'date';
                        $properties[$key]['ignore_malformed'] = true;
                    }
                }
            }

            // Add categories field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_CATEGORIES] = [
                'type' => 'string',
                'include_in_all' => true,
                'analyzer' => $this->getLanguageAnalyzer($store),
            ];

            // Add parent_ids field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_PARENT_IDS] = [
                'type' => 'integer',
                'store' => 'yes',
                'index' => 'no',
            ];

            // Add URL field
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::PRODUCT_URL] = [
                'type' => 'string',
                'store' => 'yes',
                'index' => 'no',
            ];

            // Add name autocompletion
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::NAME_SUGGESTER] = [
                'type' => 'completion',
                'analyzer' => 'std',
                'search_analyzer' => 'std'
            ];
            $properties[\Wyomind\ElasticsearchCore\Helper\Config::SKU_SUGGESTER] = [
                'type' => 'completion',
                'analyzer' => 'std',
                'search_analyzer' => 'std'
            ];

            $properties['product_weight']['type'] = 'integer';
        }

        $categoryCollection = $this->_categoryHelper->createCategoryCollection();
        foreach ($categoryCollection as $cat) {
            $properties['cat_pos_' . $cat->getId()] = [
                'type' => 'integer'
            ];
        }

        $properties['rating'] = [
            'type' => 'integer'
        ];

        // utilisé pour le tri dans layerednavigation et multifacetedautocomplete
        if ($compatibility >= 6) {
            $properties['name']['fielddata'] = 'true';
            $properties['sku']['fielddata'] = 'true';
            $properties['name']['fields']['raw'] = ['type' => 'keyword'];
            $properties['sku']['fields']['raw'] = ['type' => 'keyword'];
        } elseif ($compatibility < 6) {
            $properties['name']['fields']['raw'] = [
                'type' => 'string',
                'index' => 'not_analyzed'
            ];
            $properties['sku']['fields']['raw'] = [
                'type' => 'string',
                'index' => 'not_analyzed'
            ];
        }

        $properties = new \Magento\Framework\DataObject($properties);

        $this->_eventManager->dispatch('wyomind_elasticsearchcore_product_index_properties', [
            'indexer' => $this,
            'store' => $store,
            'properties' => $properties,
        ]);

        return $properties->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchFields($store = null, $withBoost = false, $compatibility = null)
    {
        $type = 'text';

        if ($compatibility < 6) {
            $type = 'string';
        }

        $excludedFields = ['categories'];
        $properties = $this->getProperties($store);
        foreach ($properties as $field => $property) {
            if ($property['type'] !== $type) {
                $excludedFields[] = $field;
            }
        }

        $fields = parent::getSearchFields($store, $withBoost, $compatibility);

        return array_values(array_diff($fields, $excludedFields));
    }

    /**
     * {@inheritdoc}
     */
    public function getDynamicConfigGroups()
    {
        // indexation enabled?
        $dynamicConfigFields['enable'] = [
            'id' => 'enable',
            'translate' => 'label comment',
            'type' => 'select',
            'sortOrder' => '10',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Enable Product Index'),
            'source_model' => 'Magento\Config\Model\Config\Source\Yesno',
            'comment' => __('If enabled, products will be indexed in Elasticsearch.'),
            '_elementType' => 'field',
            'path' => 'wyomind_elasticsearchcore/types/product',
        ];

        // images size for the autocompletes
        $dynamicConfigFields['image_size'] = [
            'id' => 'image_size',
            'translate' => 'label comment',
            'type' => 'text',
            'sortOrder' => '20',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Image Size'),
            'comment' => __('Image size in px, default is 50px.'),
            'depends' => [
                'fields' => [
                    'enable' => [
                        'id' => 'wyomind_elasticsearchcore/types/product/enable',
                        'value' => '1',
                        '_elementType' => 'field',
                        'dependPath' => [
                            0 => 'wyomind_elasticsearchcore',
                            1 => 'types',
                            2 => 'product',
                            3 => 'enable'
                        ]
                    ]
                ]
            ],
            'validate' => 'required-entry validate-greater-than-zero validate-digits',
            '_elementType' => 'field',
            'path' => 'wyomind_elasticsearchcore/types/product'
        ];

        // Indexable attributes
        $dynamicConfigFields['attributes'] = [
            'id' => 'attributes',
            'translate' => 'label comment',
            'type' => 'hidden',
            'sortOrder' => '30',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => 'Attributes to index',
            '_elementType' => 'field',
            'frontend_model' => 'Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\ProductAttributes',
            'path' => 'wyomind_elasticsearchcore/types/product',
            'depends' => [
                'fields' => [
                    'enable' => [
                        'id' => 'wyomind_elasticsearchcore/types/product/enable',
                        'value' => '1',
                        '_elementType' => 'field',
                        'dependPath' => [
                            0 => 'wyomind_elasticsearchcore',
                            1 => 'types',
                            2 => 'product',
                            3 => 'enable'
                        ]
                    ]
                ]
            ]
        ];

        $dynamicConfigGroups['product'] = [
            'id' => 'product',
            'translate' => 'label',
            'sortOrder' => '30',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Product'),
            'children' => $dynamicConfigFields,
            '_elementType' => 'group',
            'path' => 'wyomind_elasticsearchcore/types'
        ];

        return $dynamicConfigGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'catalog_product_attribute_update_after' => [['indexer' => $this->type, 'action' => 'execute', 'getId' => 'getProductIdsFromEvent']],
            'controller_action_postdispatch_catalog_product_save' => [['indexer' => $this->type, 'action' => 'executeRow', 'getId' => 'getObjectId']],
            //'cataloginventory_stock_item_save_commit_after' => [['indexer' => $this->type, 'action' => 'executeRow', 'getId' => 'getObjectId']],
            'catalog_product_delete_after_done' => [['indexer' => $this->type, 'action' => 'deleteRow', 'getId' => 'getObjectId']],
            'wyomind_elasticsearchcore_event_reindex_catalog_category_commit_after' => [['indexer' => $this->type, 'action' => 'reindexAfterCategoryUpdate', 'getId' => 'getObserverId']],
            'review_save_commit_after' => [['indexer' => $this->type, 'action' => 'setToReindex']],
            'checkout_submit_all_after' => [['indexer' => $this->type, 'action' => 'execute', 'getId' => 'getProductIdFromOrderEvent']],
            'paypal_express_place_order_success' => [['indexer' => $this->type, 'action' => 'execute', 'getId' => 'getProductIdFromOrderEvent']],
            'paypal_ipn_submit_all_after' => [['indexer' => $this->type, 'action' => 'execute', 'getId' => 'getProductIdFromOrderEvent']],
            'pos_order_after_submit' => [['indexer' => $this->type, 'action' => 'execute', 'getId' => 'getProductIdFromOrderEvent']]
        ];
    }

    /**
     * Get the product IDs from the event (mass attribute update in the backend)
     * @param $observer
     * @return mixed
     */
    public function getProductIdsFromEvent($observer) {
        return $observer->getEvent()->getId();
    }

    /**
     * Get the product ID from the event
     * @param \Magento\Framework\Event\Observer $observer
     * @return string
     */
    public function getObjectId($observer)
    {
        if ($observer->getEvent()->getProduct() == null) {
            $return = $observer->getEvent()->getRequest()->getParam('id');
        } else {
            $return = $observer->getEvent()->getProduct()->getId();
        }
        // when creating a product, id = null;
        if ($return == null) {
            $productCollection = $this->_productCollectionFactory->create();
            $collection = $productCollection
                ->addAttributeToSelect('*')
                ->addAttributeToSort('created_at', 'DESC')
                ->setPageSize(1)
                ->load();
            $lastProduct = $collection->getFirstItem();
            if ($lastProduct) {
                $return = $collection->getFirstItem()->getId();
            }
        }
        return $return;
    }

    /**
     * Get the product ID from the review observer
     * @param \Magento\Framework\Event\Observer $observer
     * @return string
     */
    public function getProductIdFromReviewEvent($observer)
    {
        return $observer->getDataObject()->getEntityPkValue();
    }

    /**
     * Reindex products after a category update and reindex
     * @param string $categoryId
     */
    public function reindexAfterCategoryUpdate($categoryId)
    {
        $this->_excludedCategory = $categoryId;
        $productIds = [];
        $rowId = $this->moduleIsEnabled('Magento_Enterprise') ? 'row_id' : 'entity_id';

        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')->addCategoriesFilter(['eq' => $categoryId]);

        foreach ($productCollection->getData() as $product) {
            $productIds[] = $product[$rowId];
        }

        $sessionIds = $this->_sessionHelper->getIdsToReindex('productIdToReindexBeforeCategoryUpdate');
        $ids = array_unique(array_merge($sessionIds, $productIds));

        $this->_eventExcludedProducts = array_diff($sessionIds, $productIds);

        $this->execute($ids);

        // Reset the session ids
        $this->_sessionHelper->setIdsToReindex('productIdToReindexBeforeCategoryUpdate', []);
    }

    /**
     * Save the product id to reindex in the "to_reindex" database table
     * @param string $objectId
     */
    public function setToReindex($objectId)
    {
        $review = $this->_reviewFactory->create()->load($objectId);

        $productId = $review->getEntityPkValue();

        /** @var \Wyomind\ElasticsearchCore\Model\ToReindex $toReindexModel */
        $toReindexModel = $this->_toReindexModelFactory->create();
        $toReindexModel->setIndexerId($this->type);
        $toReindexModel->setToReindex($productId);
        $toReindexModel->save();

        $index = $this->_indexModel->loadByIndexerId($this->type);
        $index->setReindexed(0);
        $index->save();
    }

    /**
     * Get the product IDs from the order observer
     * @param \Magento\Framework\Event\Observer $observer
     * @return array
     */
    public function getProductIdFromOrderEvent($observer)
    {
        $productIds = [];
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllVisibleItems();

        foreach ($items as $item) {
            $productIds[] = $item->getProductId();
        }

        return $productIds;
    }

    /**
     * Format prices as floats
     */
    public function convertPrice(&$value)
    {
        if (is_numeric($value)) {
            $value = (float)$value;
        }
    }

    public function compareRequestPaths($a, $b)
    {
        return (count(explode('/', $a)) < count(explode('/', $b))) ? -1 : 1;
    }


    public function getActionColumn()
    {
        return [];
    }

    public function getBrowseActions(&$item, $name)
    {
        $item[$name]['edit'] = [
            'href' => $this->_urlBuilder->getUrl(
                'catalog/product/edit',
                ['id' => $item['id']]
            ),
            'label' => __('Edit')
        ];
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getBrowseColumns($storeId)
    {
        $columns = [];

        $columns['image'] = $this->addBrowseColumn('image', 'image', 'Image', 20);
        $columns['sku'] = $this->addBrowseColumn('html', 'sku', 'Sku', 30);
        $columns['name'] = $this->addBrowseColumn('html', 'name', 'Product Name', 40);
        $columns['url'] = $this->addBrowseColumn('url', 'url', 'Url', 50);
        $columns['type_id'] = $this->addBrowseColumn('html', 'type_id', 'Type', 60);
        $columns['visibility'] = $this->addBrowseColumn('html', 'visibility', 'Visibility', 70);
        $columns['price'] = $this->addBrowseColumn('price', 'price', 'Price', 80);

        return $columns;
    }
}