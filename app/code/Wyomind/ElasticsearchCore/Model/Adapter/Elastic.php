<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Adapter;

/**
 * Elasticsearch Adapter
 * Rewrites the products collection retrieved when performing a search (for the search results page)
 */
class Elastic extends \Magento\Framework\Search\Adapter\Mysql\Adapter
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder
     */
    private $aggregationBuilder;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\DocumentFactory
     */
    protected $documentFactory;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Mapper
     */
    protected $mapper;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $configHelper;

    /**
     * Elastic constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder $aggregationBuilder
     * @param \Magento\Framework\Search\Adapter\Mysql\DocumentFactory $documentFactory
     * @param \Magento\Framework\Search\Adapter\Mysql\Mapper $mapper
     * @param \Magento\Framework\Search\Adapter\Mysql\ResponseFactory $responseFactory
     * @param \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Data $dataHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder $aggregationBuilder,
        \Magento\Framework\Search\Adapter\Mysql\DocumentFactory $documentFactory,
        \Magento\Framework\Search\Adapter\Mysql\Mapper $mapper,
        \Magento\Framework\Search\Adapter\Mysql\ResponseFactory $responseFactory,
        \Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory $temporaryStorageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Data $dataHelper,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper
    )
    {
        $this->resource = $resource;
        $this->aggregationBuilder = $aggregationBuilder;
        $this->documentFactory = $documentFactory;
        $this->mapper = $mapper;
        $this->responseFactory = $responseFactory;
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->configHelper = $configHelper;
        parent::__construct($mapper, $responseFactory, $resource, $aggregationBuilder, $temporaryStorageFactory);
    }

    /**
     * @inheritdoc
     */
    public function query(\Magento\Framework\Search\RequestInterface $request)
    {
        $query = $request->getQuery();

        // category page?
        if ($query->getName() == 'catalog_view_container' || $query->getName() == 'quick_order_suggestions_search_container') {
            return parent::query($request);
        }

        // ES down?
        if (!$this->configHelper->getServerStatus()) {
            return parent::query($request);
        }

        try {
            $filters = $this->getFiltersFromQuery($query);
            $elasticResponse = $this->requestElastic($request, $filters);
        } catch (\Exception $e) { // config file not found => fallback to mysql
            return parent::query($request);
        }

        $customerGroupId = $this->dataHelper->getCustomerGroupId();
        $temporaryStorage = $this->temporaryStorageFactory->create();

        $score = count($elasticResponse['products']);
        $elasticDocuments = [];
        foreach ($elasticResponse['products'] as $product) {
            $elasticDocuments[] = $this->documentFactory->create([
                'entity_id' => (int)$product['id'],
                'score' => $score,
            ]);
            $score--;
        }

        $table = $temporaryStorage->storeDocuments($elasticDocuments);

        $documents = $this->getDocuments($table);

        $aggregations = $this->aggregationBuilder->build($request, $table);

        foreach ($aggregations as $bucket => $agg) {
            foreach ($agg as $option => $info) {
                $aggregations[$bucket][$option]['count'] = 0;
            }
        }

        foreach ($elasticResponse['products'] as $product) {
            if ($product['visibility'] > 1) {
                foreach ($aggregations as $bucket => $agg) {
                    if ($bucket != 'price_bucket' && $bucket != 'category_bucket') {
                        if (isset($product[str_replace('_bucket', '', $bucket) . '_ids'])) {
                            $values = $product[str_replace('_bucket', '', $bucket) . '_ids'];

                            if (!is_array($values)) {
                                $value = $values;
                                if (isset($aggregations[$bucket][$value]['count'])) {
                                    $aggregations[$bucket][$value]['count']++;
                                }
                            } else {
                                foreach ($values as $value) {
                                    if (isset($aggregations[$bucket][$value]['count'])) {
                                        $aggregations[$bucket][$value]['count']++;
                                    }
                                }
                            }
                        }
                    } else if ($bucket == 'category_bucket') {
                        if (isset($product['categories_ids'])) {
                            $values = $product['categories_ids'];

                            if (!is_array($values)) {
                                $value = $values;
                                if (isset($aggregations[$bucket][$value]['count'])) {
                                    $aggregations[$bucket][$value]['count']++;
                                } else {
                                    $aggregations[$bucket][$value]['count'] = 1;
                                    $aggregations[$bucket][$value]['value'] = $value;
                                }
                            } else {
                                foreach ($values as $value) {
                                    if (isset($aggregations[$bucket][$value]['count'])) {
                                        $aggregations[$bucket][$value]['count']++;
                                    } else {
                                        $aggregations[$bucket][$value]['count'] = 1;
                                        $aggregations[$bucket][$value]['value'] = $value;
                                    }
                                }
                            }
                        }
                    } else if ($bucket == 'price_bucket') {
                        if (isset($product['prices_' . $customerGroupId])) {
                            $values = $product['prices_' . $customerGroupId]['final_price'];
                            foreach ($agg as $interval) {
                                $int = explode('_', $interval['value']);
                                if ($int[0] === '*') {
                                    $int[0] = 0;
                                }
                                if (!isset($int[1]) || $int[1] === '*') {
                                    $int[1] = INF;
                                }
                                if ($values >= $int[0] && $values <= $int[1]) {
                                    $aggregations[$bucket][$interval['value']]['count']++;
                                }
                            }
                        }
                    }
                }
            }
        }
        foreach ($aggregations as $bucket => $agg) {
            //if ($bucket != "price_bucket" && $bucket != "category_bucket") {
            foreach ($agg as $option => $info) {
                if ($aggregations[$bucket][$option]['count'] == 0) {
                    unset($aggregations[$bucket][$option]);
                }
            }
            //}
        }

        $response = [
            'total'=> count($documents),
            'documents' => $documents,
            'aggregations' => $aggregations,
        ];


        return $this->responseFactory->create($response);
    }

    /**
     * Perform the request using Elasticsearch
     * @param \Magento\Framework\Search\RequestInterface $request
     * @param array $filters
     * @return array
     * @throws \Exception when the config file for the storeview is not found
     */
    public function requestElastic(
        \Magento\Framework\Search\RequestInterface $request,
        $filters
    )
    {
        $dimension = current($request->getDimensions());
        if ($dimension && $dimension->getName() == 'scope') {
            $storeId = $dimension->getValue();
        } else {
            $storeId = $this->getCurrentStore()->getId();
        }

        $storeCode = $this->storeManager->getStore($storeId)->getCode();
        try {
            $config = new \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config($storeCode);
        } catch (\Exception $e) {
            throw $e;
        }
        if (!$config->getData()) {
            throw new \Exception(__('Could not find config for autocomplete'));
        }

        $boolQuery = $request->getQuery();
        $should = $boolQuery->getShould();
        $matchQuery = $should['search'];

        $client = new \Wyomind\ElasticsearchCore\Model\Client($config);
        $client->init($storeId);
        $cache = new \Wyomind\ElasticsearchCore\Helper\Cache\FileSystem();
        $synonymsHelper = new \Wyomind\ElasticsearchCore\Helper\Synonyms();
        $requester = new \Wyomind\ElasticsearchCore\Helper\Requester($client, $config, $cache, $synonymsHelper);

        $customerGroupId = $this->dataHelper->getCustomerGroupId();

        $result = $requester->getProducts($storeCode, $customerGroupId, -1, $matchQuery->getValue(), 0, 10000, 'relevance', 'desc', $filters);

        return $result;
    }

    /**
     * Extract the filters to apply from the query
     * @param \Magento\Framework\Search\Request\Query $query
     * @return array
     */
    private function getFiltersFromQuery($query)
    {
        $filters = [];
        $should = $query->getShould();
        $must = $query->getMust();

        foreach ($should as $key => $info) {
            if ($info instanceof \Magento\Framework\Search\Request\Query\Filter) {
                $reference = $info->getReference();
                if ($reference instanceof \Magento\Framework\Search\Request\Filter\Term) {
                    $field = $reference->getField();
                    if ($field == 'category_ids') {
                        $field = 'categories';
                    }
                    if ($field == 'visibility') {
                        continue;
                    }
                    $value = $reference->getValue();
                    if (isset($value['in'])) {
                        $value = $value['in'];
                    }

                    $filters[$field . '_ids'] = [$value];
                } elseif ($reference instanceof \Magento\Framework\Search\Request\Filter\Range) {
                    $field = $reference->getField();
                    if ($field == 'price') {
                        $field = 'final_price';
                    }
                    $filters[$field] = [
                        'min' => $reference->getFrom(),
                        'max' => $reference->getTo()
                    ];
                }
            }
        }

        foreach ($must as $key => $info) {
            if ($info instanceof \Magento\Framework\Search\Request\Query\Filter) {
                $reference = $info->getReference();
                if ($reference instanceof \Magento\Framework\Search\Request\Filter\Term) {

                    $field = $reference->getField();
                    if ($field == 'category_ids') {
                        $field = 'categories';
                    }
                    if ($field == 'visibility') {
                        continue;
                    }
                    $value = $reference->getValue();
                    if (isset($value['in'])) {
                        $value = $value['in'];
                    }

                    $filters[$field . '_ids'] = [$value];
                } elseif ($reference instanceof \Magento\Framework\Search\Request\Filter\Range) {
                    $field = $reference->getField();
                    if ($field == 'price') {
                        $field = 'final_price';
                    }
                    $filters[$field] = [
                        'min' => $reference->getFrom(),
                        'max' => $reference->getTo()
                    ];
                }
            }
        }

        return $filters;
    }

    /**
     * @inheritdoc
     */
    private function getDocuments(\Magento\Framework\DB\Ddl\Table $table)
    {
        $connection = $this->getConnection();
        $select = $connection->select();
        $select->from($table->getName(), ['entity_id', 'score']);

        return $connection->fetchAssoc($select);
    }

    /**
     * @inheritdoc
     */
    private function getConnection()
    {
        return $this->resource->getConnection();
    }
}