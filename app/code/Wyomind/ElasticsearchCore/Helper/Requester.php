<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;


class Requester
{
    /**
     * @var \Wyomind\ElasticsearchCore\Model\Client
     */
    protected $_client;

    /**
     * @var int
     */
    protected $_compatibility = 6;

    /**
     * @var string
     */
    protected $_index = '';

    /**
     * @var array
     */
    protected $_priceFilterValues = [];

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config
     */
    protected $_config = null;

    /**
     * attribute to remove from the results (because not useful)
     */
    protected $_attributeToRemove = ['name_suggester', 'sku_suggester', 'url_key', 'status', 'product_weight', 'options_container'];

    /**
     * @var array
     */
    protected $_attributeOptions = [];

    /**
     * @var \Monolog\Logger
     */
    protected $_logger = null;

    /**
     * @var bool
     */
    private $_logEnabled = false;

    /**
     * @var int
     */
    private $_customerGroupId = 0;

    /**
     * @var array
     */
    private $_synonyms = [];

    /**
     * @var null|Synonyms
     */
    private $_synonymsHelper = null;

    /**
     * @var Cache\AbstractCache
     */
    protected $_cache = null;

    /**
     * @param \Wyomind\ElasticsearchCore\Model\Client $client
     * @param \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config $config
     * @param Cache\AbstractCache $cache
     * @param Synonyms $synonymsHelper
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Model\Client $client,
        Autocomplete\Config $config,
        Cache\AbstractCache $cache,
        Synonyms $synonymsHelper
    )
    {
        $this->_compatibility = $config->getCompatibility();
        $this->_config = $config;
        $this->_client = $client;
        $this->_cache = $cache;
        $this->_synonymsHelper = $synonymsHelper;

        $this->checkLogFlag();
    }

    /**
     * @param array $params
     * @param string $order
     * @param string $direction
     * @param int $categoryId
     */
    public function buildOrder(&$params, $order, $direction, $categoryId = -1)
    {
        // order
        if ($order == 'position') {
            $params['body']['sort'] = [
                "cat_pos_$categoryId" => [
                    'order' => $direction
                ]
            ];
        } else if ($order == 'name') {
            $params['body']['sort'] = [
                'name.raw' => [
                    'order' => $direction
                ]
            ];
        } else if ($order == 'price') {
            $params['body']['sort'] = [
                'prices_' . $this->_customerGroupId . '.final_price' => [
                    'order' => $direction
                ]
            ];
        } else if ($order == 'rating') {
            $params['body']['sort'] = [
                'rating' => [
                    'order' => $direction
                ]
            ];
        } else if ($order == 'score') {
            $params['body']['sort'] = [
                '_score' => [
                    'order' => $direction
                ]
            ];
        }
    }

    /**
     * @param string $storeCode
     * @param int $categoryId
     * @param int $from
     * @param int $size
     * @param string $order
     * @param string $direction
     * @param array $filters
     * @param boolean $loadFilters
     * @param boolean $loadBuckets
     * @return array
     */
    public function categoryListing($storeCode, $categoryId, $from = 0, $size = 9, $order = 'position', $direction = 'asc', $filters = [], $loadFilters = true, $loadBuckets = true)
    {
        $start = microtime(true);


        $md5func = "md5";
        $md5 = $md5func($storeCode . $categoryId . $from . $size . $order . $direction . print_r($filters, true) . ($loadFilters ? "1" : "0") . ($loadBuckets ? "1" : "0"));
        $data = $this->_cache->get($md5);
        if ($data) {
            $data['time'] = round((microtime(true) - $start) * 1000);
            $data['cache'] = true;
            return $data;
        }

        // ELS REQUEST
        $params = [
            'from' => $from,
            'body' => [
                'sort' => [],
                'query' => [
                    'function_score' => [
                        'query' => [
                            'bool' => [
                                'filter' => [
                                    [
                                        'terms' => [
                                            'visibility' => [2, 3, 4] // all except not visible individually
                                        ]
                                    ],
                                    [
                                        'terms' => [
                                            'categories_ids' => [$categoryId] // category page => filter by category
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];


        // add limit to the ES request
        $this->buildLimit($params, $size);
        // add ordering to the ES request
        $this->buildOrder($params, $order, $direction, $categoryId);

        // copy of the ES request without any filter
        $withoutFilterParams = json_decode(json_encode($params), true); // decode(encode) to avoid references variables override ($query)
        // apply the filters to the ES request
        $selectedFilters = []; // selected filters in the layered navigation
        $this->applyFilters($selectedFilters, $filters, $params);

        // execute the request
        $results = $this->_client->query($this->_index, 'product', $params);

        // get the products
        $filteredProducts = $results['hits']['hits'];

        // products matching the current page only
        $slicedProducts = [];
        $this->postProcessProducts($slicedProducts, $filteredProducts);

        // PAGING
        if ($this->_compatibility <= 6) {
            $amount = $this->buildPaging($results['hits']['total'], $size, $from);
        } else {
            $amount = $this->buildPaging($results['hits']['total']['value'], $size, $from);
        }

        // FILTERS BUCKETS
        // modify the ES query to retrieve the maximum of products (10k)
        $withoutFilterParams['size'] = 10000;
        $withoutFilterParams['from'] = 0;
        $results = $this->_client->query($this->_index, 'product', $withoutFilterParams);
        $products = $results['hits']['hits'];
        // aggregations of all product attributes values
        if ($loadBuckets) {
            $buckets = $this->buildBuckets($withoutFilterParams, $products, $filters, $categoryId);
        } else {
            $buckets = [];
        }


        // FINAL RESULT
        $data = [
            'products' => $slicedProducts,
            'amount' => $amount,
            'aggregations' => $buckets,
            'selectedFilters' => $loadFilters ? $selectedFilters : [],
            'time' => round((microtime(true) - $start) * 1000)
        ];
        $this->_cache->put($md5, $data);
        return $data;
    }

    /**
     * @param string $storeCode
     * @param string $searchTerm
     * @param int $from
     * @param int $size
     * @param string $order
     * @param string $direction
     * @param array $filters
     * @param bool $loadFilters
     * @param bool $loadBuckets
     * @param bool $highlightEnabled
     * @return array
     */
    public function searchListing($storeCode, $searchTerm, $from = 0, $size = 9, $order = 'position', $direction = 'asc', $filters = [], $loadFilters = true, $loadBuckets = true, $highlightEnabled = true)
    {


        $start = microtime(true);

        $md5func = "md5";
        $md5 = $md5func($storeCode . $searchTerm . $from . $size . $order . $direction . print_r($filters, true) . ($loadFilters ? "1" : "0") . ($loadBuckets ? "1" : "0") . ($highlightEnabled ? "1" : "0"));
        $data = $this->_cache->get($md5);
        if ($data) {
            $data['time'] = round((microtime(true) - $start) * 1000);
            $data['cache'] = true;
            return $data;
        }


        // ELS REQUEST
        $query = [
            'bool' => [
                'filter' => [
                    [
                        'terms' => [
                            'visibility' => [3, 4] // all except not visible individually
                        ]
                    ]
                ]
            ]
        ];

        $params = [
            'from' => $from,
            'body' => [
                'sort' => [],
                'query' => &$query
            ]
        ];

        $type = $this->_config->getValue('types/product');

        $docs = [];
        if ($searchTerm != '') {
            $tmpParams = $this->build($searchTerm, $type, $storeCode);
            $tmpParams['body']['_source'] = ['id', 'parent_ids', 'prices_' . $this->_customerGroupId, 'visibility'];

            $response = $this->_client->query($this->_index, 'product', $tmpParams);
//var_dump(json_encode($tmpParams));
//var_dump($response);
            foreach ($response['hits']['hits'] as $doc) {
                $data = $doc['_source'];

                if ($this->validateResult($data)) {
                    $docs[$doc['_id']] = $doc['_score'];
                }

                // getting score from the maximum score between the children products
                if (isset($data[Config::PRODUCT_PARENT_IDS])) {
                    foreach ($data[Config::PRODUCT_PARENT_IDS] as $parentId) {
                        if (isset($docs[$parentId])) {
                            $doc['_score'] = max($doc['_score'], $docs[$parentId]);
                        }
                        $docs[$parentId] = $doc['_score'];
                    }
                }
            }
            $ids = [];
            $scores = [];
            $docsNew = [];

            foreach ($docs as $id => $score) {
                $docsNew[] = ['id' => $id, 'score' => $score];
                $ids[$id] = $id;
                $scores[$id] = $score;
            }
            array_multisort($scores, SORT_DESC, $ids, SORT_ASC, $docsNew);
            $docs = [];
            foreach ($docsNew as $doc) {
                $docs[] = $doc['id'];
            }

            $params = [
                'from' => $from,
                'body' => [
                    'sort' => [],
                    'query' => [
                        'function_score' => [
                            'query' => &$query
                        ]
                    ]
                ]
            ];

            $score = count($docs);

            if ($score > 0) {
                if ($this->_compatibility == 2) {
                    $source = "if (doc['sku'].value == \"" . trim($searchTerm) . "\") return 999; else return -ids.indexOf(doc['id'].value.intValue());";
                    $query = ['ids' => ['values' => $docs]];
                    $params['body']['query']['function_score']['script_score']['script']['lang'] = 'groovy';
                    $params['body']['query']['function_score']['script_score']['script']['inline'] = $source;
                    $params['body']['query']['function_score']['script_score']['script']['params']['ids'] = $docs;
                } else {
//                    $source = "if (params['_source']['sku'] == \"" . trim($searchTerm) . "\") return 999; else return " . $score . "-params.ids.indexOf(doc['id'].value.intValue());";
//                    $params['body']['query']['function_score']['boost_mode'] = "replace";
//                    $query['bool']['filter'][] = ['terms' => ['id' => $docs]];
//                    $params['body']['query']['function_score']['script_score']['script']['lang'] = 'painless';
//                    $params['body']['query']['function_score']['script_score']['script']['source'] = $source;
//                    $params['body']['query']['function_score']['script_score']['script']['params']['ids'] = $docs;
                    // ES <= 5.2
                    $source = "if (params['_source']['sku'] == \"" . trim($searchTerm) . "\") return 999; else return " . $score . "-params.ids.indexOf(doc['id'].value.intValue());";
                    $params['body']['query']['function_score']['boost_mode'] = "replace";
                    $query['bool']['filter'][] = ['terms' => ['id' => $docs]];
                    $params['body']['query']['function_score']['script_score']['script']['lang'] = 'painless';
                    $params['body']['query']['function_score']['script_score']['script']['inline'] = $source;
                    $params['body']['query']['function_score']['script_score']['script']['params']['ids'] = $docs;
                }

            }
        }

        if (count($docs)) {

            // add limit to the ES request
            $this->buildLimit($params, $size);
            // add ordering to the ES request
            $this->buildOrder($params, $order, $direction);

            // copy of the ES request without any filter
            $withoutFilterParams = json_decode(json_encode($params), true); // decode(encode) to avoid references variables override ($query)
            // apply the filters to the ES request
            $selectedFilters = []; // selected filters in the layered navigation
            $this->applyFilters($selectedFilters, $filters, $params);

            //$this->addHighlight($params, $searchTerm, $type['search_fields']);

            // execute the request
            $results = $this->_client->query($this->_index, 'product', $params);
            // get the products
            $filteredProducts = $results['hits']['hits'];

//            if ($filteredProducts[0]['_source']['sku'] == trim($searchTerm)) {
//                $filteredProducts = [$filteredProducts[0]];
//            }

            if ($highlightEnabled) {
                $this->postProcess($filteredProducts, $searchTerm);
            }

            // products matching the current page only
            $slicedProducts = [];
            $this->postProcessProducts($slicedProducts, $filteredProducts);
            // PAGING
            if ($this->_compatibility <= 6) {
                $amount = $this->buildPaging($results['hits']['total'], $size, $from);
            } else { // 7
                $amount = $this->buildPaging($results['hits']['total']['value'], $size, $from);
            }
            // FILTERS BUCKETS
            // modify the ES query to retrieve the maximum of products (10k)
            $withoutFilterParams['size'] = 10000;
            $withoutFilterParams['from'] = 0;

            $results = $this->_client->query($this->_index, 'product', $withoutFilterParams);
            $products = $results['hits']['hits'];

            // aggregations of all product attributes values
            if ($loadBuckets) {
                $buckets = $this->buildBuckets($withoutFilterParams, $products, $filters);
            } else {
                $buckets = [];
            }
            // FINAL RESULT
            $data = [
                //'scores' => $scores,
                'synonyms' => $this->_synonyms,
                'products' => $slicedProducts,
                'amount' => $amount,
                'aggregations' => $buckets,
                'selectedFilters' => $loadFilters ? $selectedFilters : [],
                'time' => round((microtime(true) - $start) * 1000)
            ];
            $this->_cache->put($md5, $data);
            return $data;
        } else {
            return [
                'synonyms' => $this->_synonyms,
                'products' => [],
                'amount' => 0,
                'aggregations' => [],
                'selectedFilters' => [],
                'time' => round((microtime(true) - $start) * 1000)
            ];
        }
    }

    /**
     * @param string $storeCode
     * @param string $type
     * @param string $searchTerm
     * @param $size
     * @param bool $highlightEnabled
     * @return array
     */
    public function searchByType($storeCode, $type, $searchTerm, $size, $highlightEnabled = true)
    {
        $start = microtime(true);

        $md5func = "md5";
        $md5 = $md5func($storeCode . $searchTerm . $type . $searchTerm . $size . ($highlightEnabled ? "1" : "0"));
        $data = $this->_cache->get($md5);
        if ($data) {
            $data['time'] = round((microtime(true) - $start) * 1000);
            $data['cache'] = true;
            return $data;
        }

        $this->_index = $this->_client->getIndexName($storeCode, false, $type);
        $typeConfig = $this->_config->getValue('types/' . $type);
        $params = $this->build($searchTerm, $typeConfig, $storeCode);

        // add limit to the ES request
        $this->buildLimit($params, $size);
        // add ordering to the ES request
        $this->buildOrder($params, 'score', 'desc');

        //$this->addHighlight($params, $searchTerm, $typeConfig['search_fields']);

        // execute the request
        $results = $this->_client->query($this->_index, $type, $params);


        // PAGING
        if ($this->_compatibility <= 6) {
            $amount = $this->buildPaging($results['hits']['total'], $size, 0);
        } else { // 7
            $amount = $this->buildPaging($results['hits']['total']['value'], $size, 0);
        }

        // FINAL DOCS
        $docs = $results['hits']['hits'];
        if ($highlightEnabled) {
            $this->postProcess($docs, $searchTerm);
        }

        $data = [
            'count' => $amount['total'],
            'docs' => array_map(function ($doc) {
                return $doc['_source'];
            }, $docs),
            'time' => round((microtime(true) - $start) * 1000)
        ];
        $this->_cache->put($md5, $data);
        return $data;
    }

    /**
     * @param string $storeCode
     * @param string $searchTerm
     * @param int $size
     * @return array
     */
    public function getSuggestions($storeCode, $searchTerm, $size)
    {
        $start = microtime(true);

        $md5func = "md5";
        $md5 = $md5func($storeCode . $searchTerm . $searchTerm . $size);
        $data = $this->_cache->get($md5);
        if ($data) {
            $data['time'] = round((microtime(true) - $start) * 1000);
            $data['cache'] = true;
            return $data;
        }

        $suggestsResults = [];


        foreach ($this->_synonyms as $syno) {
            if ($syno != $searchTerm && count($suggestsResults) < $size) {
                $tmpData = $this->getProducts($storeCode, 0, -1, $syno, 0, 1, 'score', 'asc', [], false, false, false);
                $suggestsResults[$syno] = ['text' => $syno, 'count' => $tmpData['amount']['total']];
            }
        }


        $this->_index = $this->_client->getIndexName($storeCode, false, 'product');
        $suggestParams = [
            'body' => [
                'suggest' => [
                    'name' => [
                        'text' => $searchTerm,
                        'phrase' => [
                            'field' => 'name',
                            'gram_size' => 1,
                            'max_errors' => 0.9,
                            'direct_generator' => [
                                [
                                    'field' => 'name',
                                    'min_word_length' => 3
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];


        $response = $this->_client->query($this->_index, 'product', $suggestParams);
        if (isset($response['suggest']) && isset($response['suggest']['name'])) {
            foreach ($response['suggest']['name'] as $suggests) {
                foreach ($suggests['options'] as $option) {
                    if ($option['text'] != $searchTerm && count($suggestsResults) < $size && !in_array($option['text'], array_keys($suggestsResults))) {
                        $tmpData = $this->getProducts($storeCode, 0, -1, $option['text'], 0, 1, 'score', 'asc', [], false, false, false);
                        $suggestsResults[$option['text']] = ['text' => $option['text'], 'count' => $tmpData['amount']['total']];
                    }
                }
            }
        }

        $result = array_slice($suggestsResults, 0, $size);

        $data = ['docs' => $result, 'count' => count($result), 'time' => round((microtime(true) - $start) * 1000)];
        $this->_cache->put($md5, $data);
        return $data;
    }

    /**
     * @param string $storeCode
     * @param int $categoryId
     * @param string $searchTerm
     * @param int $from
     * @param int $size
     * @param string $order
     * @param string $direction
     * @param array $filters
     * @param bool $loadFilters
     * @param bool $loadBuckets
     * @param bool $highlightEnabled
     * @return array
     */
    public function getProducts($storeCode, $customerGroupId, $categoryId, $searchTerm, $from = 0, $size = 9, $order = 'position', $direction = 'asc', $filters = [], $loadFilters = true, $loadBuckets = true, $highlightEnabled = true)
    {

        $this->_customerGroupId = $customerGroupId;

        $this->_index = $this->_client->getIndexName($storeCode, false, 'product');

        $this->_attributeOptions = $this->_config->getValue('swatches'); // attribute options configuration (label <=> option id <=> swatch? text? ...

        if ($categoryId != -1) {
            return $this->categoryListing($storeCode, $categoryId, $from, $size, $order, $direction, $filters, $loadFilters, $loadBuckets);
        }

        if ($searchTerm != '') {
            return $this->searchListing($storeCode, $searchTerm, $from, $size, $order, $direction, $filters, $loadFilters, $loadBuckets, $highlightEnabled);
        }
    }

    /**
     * @param array $results
     * @param string $searchTerm
     */
    public function postProcess(&$results, $searchTerm)
    {
        $terms = array_filter(explode(" ", str_replace("|", " ", $searchTerm)));

        foreach ($results as $key => $result) {
            foreach ($result['_source'] as $field => $values) {
                if (strpos($field, 'image') === false && strpos($field, 'identifier') === false && strpos($field, 'url') === false && !in_array($field, ['type_id'])) {
                    if (is_string($values)) {
                        if ($field === "name") {
                            $results[$key]['_source'][$field . "_no_highlight"] = $values;
                        }
                        $pattern = '/(' . str_replace(["(", ")", ".", "]", "[", "/"], ["\\(", "\\)", "\\.", "\\]", "\\[", "\\/"], implode("|", $terms)) . ')+/i';
                        $matches = [];
                        preg_match_all($pattern, $values, $matches, PREG_OFFSET_CAPTURE);
                        if (count($matches[0])) {
                            $matchTerms = array_reverse($matches[0]);
                            foreach ($matchTerms as $match) {
                                $term = $match[0];
                                $length = strlen($term);
                                $offset = $match[1];
                                $values = substr_replace($values, "<span class='highlight wyomind-secondary-bgcolor'>" . $term . '</span>', $offset, $length);
                            }
                        }
                        $results[$key]['_source'][$field] = $values;
                    }
                }
            }
        }
    }

    /**
     * @param array $slicedProducts
     * @param array $filteredProducts
     */
    public function postProcessProducts(&$slicedProducts, $filteredProducts)
    {


        foreach ($filteredProducts as $product) {
            $source = $product['_source'];

            // remove not useful attributes
            foreach ($this->_attributeToRemove as $attribute) {
                if (isset($source[$attribute])) {
                    unset($source[$attribute]);
                }
            }

            foreach (array_keys($source) as $key) {
                if (strpos($key, 'cat_pos_') !== false) {
                    unset($source[$key]);
                }

                if (strpos($key, 'prices_') === 0) {
                    if ($key != 'prices_' . $this->_customerGroupId) {
                        unset($source[$key]);
                    }
                }
            }


            // retrieve information for the swatch attributes
            if (isset($source['configurable_options'])) {
                foreach ($source['configurable_options'] as $key => $option) {
                    unset($source['configurable_options'][$key]);
                    if (isset($this->_attributeOptions[$option]) && isset($source[$option . '_ids'])) {
                        $source['configurable_options'][$option] = [
                            'id' => $this->_attributeOptions[$option]['id'],
                            'label' => $this->_attributeOptions[$option]['label'],
                            'visualSwatch' => $this->_attributeOptions[$option]['visualSwatch'],
                            'textSwatch' => $this->_attributeOptions[$option]['textSwatch'],
                            'values' => []
                        ];

                        foreach ($source[$option . '_ids'] as $optionValue) {
                            if (isset($this->_attributeOptions[$option][$optionValue])) {
                                if (isset($this->_attributeOptions[$option][$optionValue]['options'])) {
                                    $source['configurable_options'][$option]['values'][] = [
                                        'id' => $optionValue,
                                        'label' => $this->_attributeOptions[$option][$optionValue]['label'],
                                        'swatch' => $this->_attributeOptions[$option][$optionValue]['options']['swatch']
                                    ];
                                } else {
                                    $source['configurable_options'][$option]['values'][] = [
                                        'id' => $optionValue,
                                        'label' => $this->_attributeOptions[$option][$optionValue]['label'],
                                        'swatch' => $this->_attributeOptions[$option][$optionValue]['label']
                                    ];
                                }
                            }
                        }
                        // only for reply size optimization but required for multifaceted autocomplete
                        //unset($source[$option . "_ids"]);
                    }
                }
            }
            if (!empty($source['categories'])) {
                if (is_array($source['categories'])) {
                    $source['category'] = array_pop($source['categories']);
                } else {
                    $split = explode(',', $source['categories']);
                    $source['category'] = array_pop($split);
                }
            }

            $slicedProducts[] = $source;
        }
    }

    /**
     * @param $selectedFilters
     * @param $filters
     * @param $params
     */
    public function applyFilters(&$selectedFilters, $filters, &$params)
    {
        foreach ($filters as $field => $values) {
            if (empty(trim($field))) {
                continue;
            }
            $values = array_filter($values, function ($elt) {
                return $elt !== null && $elt !== "";
            });
            if (count($values)) {
                $this->addFilter($field, $values, $params, $selectedFilters);
                // keep this line in case we want to show the number of products per filter in the breadcrumb
                //$results = $this->_client->query($this->_index, 'product', $params);
                $selectedFilters[$field]['count'] = 0;// $results['hits']['total'];
            }
        }
    }

    private function addFilter($field, $values, &$params, &$selectedFilters)
    {
        if ($field == 'final_price' && isset($values['min']) && isset($values['max'])) { // price ?
            $this->_priceFilterValues = $values;
            $selectedFilters[$field] = [
                'values' => $values
            ];
            if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                $params['body']["query"]["function_score"]["query"]['bool']['filter'][] = [
                    'range' => [
                        'prices_' . $this->_customerGroupId . '.final_price' => [
                            'gte' => $values['min'],
                            'lte' => $values['max']
                        ]
                    ]
                ];
            } elseif ($this->_compatibility == 2) {
                $params['body']['filter']['bool']['must'][] = [
                    'range' => [
                        'prices_' . $this->_customerGroupId . '.final_price' => [
                            'gte' => $values['min'],
                            'lte' => $values['max']
                        ]
                    ]
                ];
            }

        } else if ($field == 'rating') { // rating ?
            $selectedFilters[$field] = [
                'values' => array_flip($values) // key = value; value = key
            ];
            // ES query
            $ratingFilters = ['bool' => ['should' => []]];
            foreach ($values as $value) {
                if ($value != -1) {
                    if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                        $ratingFilters['bool']['should'][] = [
                            'range' => [
                                'rating' => [
                                    'gte' => $value,
                                    'lt' => $value + 20
                                ]
                            ]
                        ];
                    } elseif ($this->_compatibility == 2) {
                        $params['body']['filter']['bool']['should'][] = [
                            'range' => [
                                'rating' => [
                                    'gte' => $value,
                                    'lt' => $value + 20
                                ]
                            ]
                        ];
                    }

                } else {
                    if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                        $ratingFilters['bool']['should'][] = [
                            'term' => [
                                'rating' => ' - 1'
                            ]
                        ];
                    } elseif ($this->_compatibility == 2) {
                        $params['body']['filter']['bool']['should'][] = [
                            'term' => [
                                'rating' => ' - 1'
                            ]
                        ];
                    }


                }
            }
            if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                $params['body']["query"]["function_score"]["query"]['bool']['filter'][] = $ratingFilters;
            }
        } elseif ($field == 'categories_ids') {
            if (count($values) == 1 && $values[0] == 2) return;
            $tmp = [
                'label' => 'Categories'
            ];
            $catTree = $this->_config->getCategoryTree();
            foreach ($values as $value) {
                $tmp['values'][$value] = $catTree[$value]['label'];
            }
            $selectedFilters[$field] = $tmp;
            if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                $params['body']["query"]["function_score"]["query"]['bool']['filter'][] = [
                    'terms' => [
                        $field => $values
                    ]
                ];
            } elseif ($this->_compatibility == 2) {
                $params['body']['filter']['bool']['must'][] = [
                    "terms" => [
                        $field => $values
                    ]
                ];
            }

        } else {
            // selected filters
            $actualField = str_replace('_ids', '', $field);

            $tmp = [
                'label' => (isset($this->_attributeOptions[$actualField]) ? $this->_attributeOptions[$actualField]['label'] : ""),
            ];
            foreach ($values as $k => $value) {
                if (is_array($value)) {
                    $values[$k] = $value[0];
                    $value = $value[0];
                }
                $tmp['values'][$value] = (isset($this->_attributeOptions[$actualField]) && isset($this->_attributeOptions[$actualField][$value]) ? $this->_attributeOptions[$actualField][$value]['label'] : "");
            }
            $selectedFilters[$field] = $tmp;
            if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
//                foreach ($values as $value) {
//                    $params['body']["query"]["function_score"]["query"]['bool']['filter'][] = [
//                        'term' => [
//                            $field => $value
//                        ]
//                    ];
//                }
                $params['body']["query"]["function_score"]["query"]['bool']['filter'][] = [
                    'terms' => [
                        $field => array_values($values)
                    ]
                ];
            } elseif ($this->_compatibility == 2) {
                $params['body']['filter']['bool']['must'][] = [
                    "terms" => [
                        $field => array_values($values)
                    ]
                ];
            }
        }

    }

    /**
     * @param array $params
     * @param string $size
     */
    public function buildLimit(&$params, $size)
    {
        // size
        if ($size != 'all') {
            $params['size'] = $size;
        } else {
            $params['size'] = 10000; // 10k = max allowed size
        }
    }

    public function buildPaging($total, $size, $from)
    {
        if ($size != 'all') {
            $nbPages = ceil($total / $size);
            $current = ($from + $size) / $size;
        } else {
            $size = $total;
            $nbPages = 1;
            $current = 1;
        }

        // list of pages to display
        $pages = [];
        for ($i = 1; $i <= $nbPages; $i++) {
            $pages[] = $i;
        }
        if ($current <= 3) {
            $pages = array_slice($pages, 0, 5);
        } elseif ($current >= $nbPages - 2) {
            $pages = array_slice($pages, ($nbPages - 5 > 0) ? $nbPages - 5 : 0, 5);
        } else {
            $pages = array_slice($pages, $current - 3, 5);
        }
        $previous = $nbPages > 1 && $current != 1; // display the "previous" button?
        $next = $nbPages > 1 && $current != $nbPages; // display the "next" button?

        return ['total' => $total, 'current' => $current, 'pages' => $pages, 'previous' => $previous, 'next' => $next, 'size' => $size];
    }


    public function buildBuckets(&$params, $products, $filters, $categoryId = -1)
    {

        $buckets = [];
        $done = [];

        $this->buildBucket($buckets, $products, -1, $categoryId);

        foreach ($filters as $field => $values) {

            $values = array_filter($values, function ($elt) {
                return $elt !== null && $elt !== "";
            });

            if (empty(trim($field)) || empty($values)) {
                continue;
            }

            if (count($done)) {
                $results = $this->_client->query($this->_index, 'product', $params);
                $products = $results['hits']['hits'];
            }

            $this->buildBucket($buckets, $products, $field, $categoryId);
            $this->addFilter($field, $values, $params, $null);
            $done[] = $field;
        }
        $results = $this->_client->query($this->_index, 'product', $params);
        $products = $results['hits']['hits'];

        $this->buildBucket($buckets, $products, "", $categoryId, $done);

        if (isset($buckets['quantity_and_stock_status_ids'])) {
            $buckets['quantity_and_stock_status_ids']['label'] = 'In Stock';
            if (isset($buckets['quantity_and_stock_status_ids']['values'][1]['count'])) {
                $buckets['quantity_and_stock_status_ids']['values'][1]['label'] = 'Yes';
            }
            if (isset($buckets['quantity_and_stock_status_ids']['values'][0]['count'])) {
                $buckets['quantity_and_stock_status_ids']['values'][0]['label'] = 'No';
            }
        }

        return $buckets;

    }

    public function buildBucket(&$buckets, $products, $field, $categoryId, $done = [])
    {

        if (in_array($field, $done)) {
            return;
        }

        if ($field == 'final_price' || ($field == '' && !in_array('final_price', $done))) {
            //######################################################################
            // PRICES FILTER
            //######################################################################
            if (!empty($products)) {
                foreach ($products as $product) {
                    if (isset($product['_source']['prices_' . $this->_customerGroupId])) {
                        $value = $product['_source']['prices_' . $this->_customerGroupId]['final_price'];
                        if (!isset($buckets['final_price'][$value])) {
                            $buckets['final_price'][$value] = 1;
                        } else {
                            $buckets['final_price'][$value]++;
                        }
                    }
                }
                if (isset($buckets['final_price'])) {
                    $max = max(array_keys($buckets['final_price']));
                    $min = min(array_keys($buckets['final_price']));
                    if ($min === "") {
                        $min = 0;
                    }
                    $max = ceil($max);
                    $buckets['final_price'] = [];
                    $buckets['final_price']['max'] = $max;
                    $buckets['final_price']['min'] = $min;
                    $buckets['final_price']['values'] = empty($this->_priceFilterValues) ? ['min' => $min, 'max' => $max] : $this->_priceFilterValues;
                } else {
                    $buckets['final_price'] = ["max" => 0, "min" => 0, "values" => ['min' => 0, 'max' => 0]];
                }

            }
        }
        if ($field == 'rating' || ($field == '' && !in_array('rating', $done)) || $field == -1) {
            //######################################################################
            // RATING FILTERS
            //######################################################################
            if (!empty($products)) {
                $tmpBuckets = [];
                foreach ($products as $product) {
                    if (isset($product['_source']['rating']) && $product['_source']['rating'] > -1) {
                        $value = floor($product['_source']['rating'] / 20) * 20;
                    } else {
                        $value = -1;
                    }
                    if (!isset($tmpBuckets[$value])) {
                        $tmpBuckets[$value] = 1;
                    } else {
                        $tmpBuckets[$value]++;
                    }
                }
                krsort($tmpBuckets);
                foreach ($tmpBuckets as $key => $value) {
                    $buckets['rating'][$key] = $field == -1 ? 0 : $value;
                }
            }
        }
        if ($field == 'categories_ids' || ($field == '' && !in_array('categories_ids', $done)) || $field == -1) {
            //######################################################################
            // CATEGORIES FILTERS
            //######################################################################
            if (!empty($products)) {
                if (isset($buckets['categories'])) {
                    $tmpBuckets = $buckets['categories'];
                } else {
                    $tmpBuckets = [];
                }
                $catTree = $this->_config->getCategoryTree();
                foreach ($products as $product) {
                    if (isset($product['_source']['categories_ids'])) {
                        foreach ($product['_source']['categories_ids'] as $catId) {
                            if (isset($catTree[$catId]) && (($categoryId != -1 && stripos($catTree[$catId]['path'], '/' . $categoryId . '/') !== false) || $categoryId == -1)) {
                                if (!isset($tmpBuckets[$catId])) {
                                    $tmpBuckets[$catId] = $catTree[$catId];
                                    $tmpBuckets[$catId]['count'] = $field == -1 ? -1 : 1;
                                    $tmpBuckets[$catId]['id'] = $catId;
                                } else {
                                    if ($field != -1) {
                                        $tmpBuckets[$catId]['count']++;
                                        if ($tmpBuckets[$catId]['count'] == 0) {
                                            $tmpBuckets[$catId]['count']++;
                                        }
                                    }
                                }
                                $path = explode("/", rtrim($catTree[$catId]['path'], '/'));
                                array_pop($path);
                                foreach ($path as $id) {
                                    if ($id > 2 && isset($catTree[$id])) {
                                        if (!isset($tmpBuckets[$id])) {
                                            $tmpBuckets[$id] = $catTree[$id];
                                            $tmpBuckets[$id]['count'] = $field == -1 ? -1 : 0;
                                            $tmpBuckets[$id]['id'] = $id;
                                        } else if ($field != -1 && $tmpBuckets[$id]['count'] == -1) {
                                            $tmpBuckets[$id]['count'] = 0;
                                        }
                                        $tmpBuckets[$id]['children'] = true;
                                    }
                                }
                            }
                        }
                    }
                }
                uasort($tmpBuckets, function ($a, $b) {
                    return $a['level'] > $b['level'];
                });

                // keep the order for javascript
                $newTmpBuckets = [];
                $catI = 0;
                foreach (array_keys($tmpBuckets) as $b) {
                    $newTmpBuckets[$catI] = $b;
                    $catI++;
                }
                $buckets['categories_ordered'] = $newTmpBuckets;
                $buckets['categories'] = $tmpBuckets;
                if ($categoryId != -1) {
                    $found = false;
                    foreach ($buckets['categories'] as $catId => $info) {
                        if ($catId == $categoryId) {
                            break;
                        }
                        if (($key = array_search($catId, $newTmpBuckets)) !== false) {
                            unset($newTmpBuckets[$key]);
                        }
                    }
                }
                $buckets['categories_ordered'] = $newTmpBuckets;
                $buckets['categories'] = $tmpBuckets;
            }
        }
        if ($field != '' && $field != -1) {
            //######################################################################
            // OTHER FILTERS
            //######################################################################
            foreach ($products as $product) {
                if (isset($product['_source'][$field])) {
                    $values = $product['_source'][$field];
                    if (strpos($field, '_ids') !== FALSE) {
                        $actualKey = str_replace('_ids', '', $field);
                        if (isset($this->_attributeOptions[$actualKey])) {
                            if (!isset($buckets[$field])) {
                                $buckets[$field] = [
                                    'label' => $this->_attributeOptions[$actualKey]['label'],
                                    'visualSwatch' => $this->_attributeOptions[$actualKey]['visualSwatch'],
                                    'textSwatch' => $this->_attributeOptions[$actualKey]['textSwatch'],
                                    'values' => []
                                ];
                            }
                            if (!is_array($values)) {
                                $value = $values;
                                if (!isset($buckets[$field]['values'][$value])) {
                                    if (isset($this->_attributeOptions[$actualKey][$value])) {
                                        $buckets[$field]['values'][$value] = [
                                            'count' => 1,
                                            'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                            'type' => $this->_attributeOptions[$actualKey][$value]['options']['type'],
                                            'data' => $this->_attributeOptions[$actualKey][$value]['options']['swatch']
                                        ];
                                    }
                                } else {
                                    $buckets[$field]['values'][$value]['count']++;
                                }
                            } else {
                                foreach ($values as $value) {
                                    if (!isset($buckets[$field]['values'][$value])) {
                                        if (isset($this->_attributeOptions[$actualKey][$value])) {
                                            $buckets[$field]['values'][$value] = [
                                                'count' => 1,
                                                'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                                'type' => $this->_attributeOptions[$actualKey][$value]['options']['type'],
                                                'data' => $this->_attributeOptions[$actualKey][$value]['options']['swatch']
                                            ];
                                        }
                                    } else {
                                        $buckets[$field]['values'][$value]['count']++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($products as $product) {
                foreach ($product['_source'] as $key => $values) {
                    if (in_array($key, $done)) {
                        continue;
                    }
                    if (strpos($key, '_ids') !== FALSE) {
                        $actualKey = str_replace('_ids', '', $key);
                        if (isset($this->_attributeOptions[$actualKey])) {
                            if (!isset($buckets[$key])) {
                                $buckets[$key] = [
                                    'label' => $this->_attributeOptions[$actualKey]['label'],
                                    'visualSwatch' => $this->_attributeOptions[$actualKey]['visualSwatch'],
                                    'textSwatch' => $this->_attributeOptions[$actualKey]['textSwatch'],
                                    'values' => []
                                ];
                            }
                            if (!is_array($values)) {
                                $value = $values;
                                if (!isset($buckets[$key]['values'][$value])) {
                                    if (isset($this->_attributeOptions[$actualKey][$value])) {
                                        if (isset($this->_attributeOptions[$actualKey][$value]['options'])) {
                                            $buckets[$key]['values'][$value] = [
                                                'count' => $field == -1 ? 0 : 1,
                                                'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                                'type' => $this->_attributeOptions[$actualKey][$value]['options']['type'],
                                                'data' => $this->_attributeOptions[$actualKey][$value]['options']['swatch']
                                            ];
                                        } else {
                                            $buckets[$key]['values'][$value] = [
                                                'count' => $field == -1 ? 0 : 1,
                                                'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                                'type' => 0,
                                                'data' => $this->_attributeOptions[$actualKey][$value]['label']
                                            ];
                                        }
                                    }
                                } else {
                                    if ($field != -1) {
                                        $buckets[$key]['values'][$value]['count']++;
                                    }
                                }
                            } else {
                                foreach ($values as $value) {
                                    if (!isset($buckets[$key]['values'][$value])) {
                                        if (isset($this->_attributeOptions[$actualKey][$value])) {
                                            if (isset($this->_attributeOptions[$actualKey][$value]['options'])) {
                                                $buckets[$key]['values'][$value] = [
                                                    'count' => $field == -1 ? 0 : 1,
                                                    'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                                    'type' => $this->_attributeOptions[$actualKey][$value]['options']['type'],
                                                    'data' => $this->_attributeOptions[$actualKey][$value]['options']['swatch']
                                                ];
                                            } else {
                                                $buckets[$key]['values'][$value] = [
                                                    'count' => $field == -1 ? 0 : 1,
                                                    'label' => $this->_attributeOptions[$actualKey][$value]['label'],
                                                    'type' => 0,
                                                    'data' => $this->_attributeOptions[$actualKey][$value]['label']
                                                ];
                                            }
                                        }
                                    } else {
                                        if ($field != -1) {
                                            $buckets[$key]['values'][$value]['count']++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
        }
    }

    /**
     * @param string $q
     * @return string
     */
    private function removeToSmallQuery($q)
    {
        $splitted = explode(' ', $q);
        foreach ($splitted as $i => $split) {
            if (strlen(trim($split)) >= 2) {
                $splitted[$i] = $split;
            } else {
                $splitted[$i] = '';
            }
        }

        return implode(' ', array_filter($splitted));
    }

    /**
     * @param string $q
     * @param array $type
     * @return array
     */
    private function build($q, $type, $storeCode)
    {
        $q = $this->removeToSmallQuery($q);

        $queries = [];

        $params = [
            'from' => 0,
            'size' => 10000,
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => &$queries
                    ],
                ],
            ],
        ];


        $this->_synonyms = $this->_synonymsHelper->buildSynonymsPhrases($q, $storeCode);

        foreach ($this->_synonyms as $phrase) {
            $queries[]['multi_match'] = [
                'query' => $phrase,
                'type' => 'cross_fields',
                'fields' => $type['search_fields'],
                'lenient' => true, // ignore bad format exception
                'operator' => $this->_config->getQueryOperator(),
            ];
        }


        if ($this->_config->isFuzzyQueryEnabled()) {
            if ($this->_compatibility == 7 || $this->_compatibility == 6 || $this->_compatibility == 5) {
                foreach ($this->_synonyms as $phrase) {
                    $queries[]['match']['all'] = [
                        'query' => $phrase,
                        'operator' => $this->_config->getQueryOperator(),
                        'fuzziness' => $this->_config->getFuzzyQueryMode(),
                    ];
                }
            } elseif ($this->_compatibility == 2) {
                $queries[]['match']['_all'] = [
                    'query' => $q,
                    'operator' => $this->_config->getQueryOperator(),
                    'fuzziness' => $this->_config->getFuzzyQueryMode(),
                ];
            }
        }


        return $params;
    }


    /**
     * @param array $data
     * @param bool $catalog
     * @return bool
     */
    public function validateResult(array $data, $catalog = false)
    {
        return isset($data[Config::PRODUCT_PRICES . "_" . $this->_customerGroupId]) && isset($data['visibility']) && ($catalog ? ($data['visibility'] >= 2) : ($data['visibility'] >= 3));
    }

    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /*                        DEBUG UTILITIES                        */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Check if the log reporting is enabled
     */
    private function checkLogFlag()
    {
        if (!$this->_logEnabled) {
            if ($this->_config->isFrontendLogEnabled()) {
                $this->_logEnabled = true;
                $formatter = new \Monolog\Formatter\LineFormatter("[%datetime%] %channel% %level_name%: %message% %context% %extra%\n", 'Y - m - d H:i:s');
                $stream = new \Monolog\Handler\RotatingFileHandler(
                    BP . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'Wyomind_ElasticsearchCore_Frontend_Requests . log',
                    \Monolog\Logger::DEBUG
                );

                $stream->setFormatter($formatter);

                $this->_logger = new \Monolog\Logger('Wyomind ElasticsearchCore Requester');
                $this->_logger->pushHandler($stream);
            }
        }
    }

    /**
     * Add a message to the log file
     * @param string $message
     */
    public function __log($message)
    {
        if ($this->_logEnabled) {
            $this->_logger->addInfo($message);
        }
    }
}