<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model;

class Client
{
    /**
     * @var \Wyomind\ElasticsearchCore\Elasticsearch\Client
     */
    protected $_client = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder
     */
    protected $_clientBuilder = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\ConfigInterface
     */
    protected $_configHelper = null;

    /**
     * @var int
     */
    private $_scope = null;

    /**
     * @param \Wyomind\ElasticsearchCore\Helper\ConfigInterface $configHelper
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\ConfigInterface $configHelper
    )
    {
        $this->_clientBuilder = \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder::create();
        $this->_configHelper = $configHelper;
    }

    /**
     * Initializes client
     * @param string $storeId
     */
    public function init($storeId)
    {
        $this->_scope = $storeId;
        $hosts = $this->_configHelper->getServers($this->_scope);
        $this->_client = $this->_clientBuilder->create()->setHosts($hosts)->build();
    }

    public function info()
    {
        return $this->_client->info($this->buildParams([]));
    }

    /**
     * Indexes documents of given type in specified index
     *
     * @param array $documents
     * @param string $index
     * @param string $type
     * @return mixed
     */
    public function index(array $documents, $index, $type)
    {
        if (empty($documents)) {
            return;
        }

        $params = ['body' => []];

        $compatibility = $this->_configHelper->getCompatibility($this->_scope);
        if ($compatibility == 7) {
            foreach ($documents as $id => $document) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $index,
                        '_id' => intval($id),
                    ],
                ];
                $params['body'][] = $document;
            }
        } else {
            foreach ($documents as $id => $document) {
                $params['body'][] = [
                    'index' => [
                        '_index' => $index,
                        '_type' => $type,
                        '_id' => intval($id),
                    ],
                ];
                $params['body'][] = $document;
            }
        }


        return $this->_client->bulk($this->buildParams($params));
    }

    /**
     * Request matching documents of given type in specified index with optional params
     *
     * @param string|array $indices
     * @param array $types
     * @param array $params
     * @return array
     */
    public function query($indices, $types, array $params = [])
    {
        $params['index'] = implode(',', (array)$indices);
        //$params['type'] = implode(',', (array) $types);

        if (!isset($params['from_admin']) || (isset($params['from_admin']) && !$params['from_admin'])) {
            $isProductWeightEnabled = $this->_configHelper->isProductWeightEnabled($this->_scope);

            if ($isProductWeightEnabled && isset($params['body']['query'])) {
                $query = $params['body']['query'];
                $newQuery = [
                    'function_score' => [
                        'query' => $query,
                        'boost_mode' => 'sum',
                        'functions' => [
                            [
                                'field_value_factor' => [
                                    'field' => 'product_weight',
                                    'factor' => 10,
                                    'modifier' => 'square',
                                    'missing' => 0
                                ]
                            ]
                        ]
                    ]
                ];

                $params['body']['query'] = $newQuery;
            }
        }
        unset($params['from_admin']);

        return $this->_client->search($this->buildParams($params));
    }

    /**
     * Indicates whether given index exists or not
     *
     * @param string $index
     * @return bool
     */
    public function existsIndex($index)
    {
        $params = ['index' => $index];

        return $this->_client->indices()->exists($this->buildParams($params));
    }

    /**
     * Creates specified index with given parameters
     *
     * @param string $index
     * @param array $params
     * @return mixed
     */
    public function createIndex($index, array $params = [])
    {
        $params['index'] = $index;

        return $this->_client->indices()->create($this->buildParams($params));
    }

    public function createAlias($index, $alias)
    {
        $indices = $this->_client->indices();
        $params = [
            'index' => $index,
            'name' => $alias
        ];

        // Remove old alias if needed
        if ($indices->existsAlias($this->buildParams($params))) {
            $indices->deleteAlias($this->buildParams($params));
        }

        return $indices->putAlias($this->buildParams($params));
    }

    /**
     * Deletes documents of given type from specified index
     *
     * @param array $ids
     * @param string $index
     * @param string $type
     * @return mixed
     */
    public function delete($ids, $index, $type)
    {
        // Delete all documents from given type if no $ids is empty
        if (empty($ids)) {
            return $this->deleteType($index, $type);
        }

        $params = ['body' => []];

        $compatibility = $this->_configHelper->getCompatibility($this->_scope);
        if ($compatibility == 7) {
            foreach ($ids as $id) {
                $params['body'][] = [
                    'delete' => [
                        '_index' => $index,
                        '_id' => intval($id),
                    ],
                ];
            }
        } else {
            foreach ($ids as $id) {
                $params['body'][] = [
                    'delete' => [
                        '_index' => $index,
                        '_type' => $type,
                        '_id' => intval($id),
                    ],
                ];
            }
        }


        return $this->_client->bulk($this->buildParams($params));
    }

    /**
     * Deletes specified index if exists
     *
     * @param string $index
     * @return mixed
     */
    public function deleteIndex($index)
    {
        return $this->_client->indices()->delete($this->buildParams(['index' => $index]));
    }

    /**
     * Deletes all documents of given type from specified index
     *
     * @param string $index
     * @param string $type
     * @return array
     */
    protected function deleteType($index, $type)
    {
        $compatibility = $this->_configHelper->getCompatibility($this->_scope);

        if ($compatibility == 7) {
            $params = [
                'scroll' => '30s',
                'size' => 500,
                'index' => $index,
                'sort' => ['_doc'], // recommended for fast process
                'body' => [
                    'query' => [
                        'match_all' => new \stdClass()
                    ],
                    'stored_fields' => [], // we only need _id
                ],
            ];
        } else {
            $params = [
                'scroll' => '30s',
                'size' => 500,
                'index' => $index,
                'type' => $type,
                'sort' => ['_doc'], // recommended for fast process
                'body' => [
                    'query' => [
                        'match_all' => new \stdClass()
                    ],
                    'stored_fields' => [], // we only need _id
                ],
            ];
        }


        $info = $this->info();
        $serverVersion = $info['version']['number'];

        if ($compatibility == 2) {
            $params['body']['query']['match_all'] = [];
            unset($params['body']['stored_fields']);
            $params['body']['fields'] = [];
        }

        $response = $this->_client->search($this->buildParams($params));

        while (true) {
            if (!count($response['hits']['hits'])) {
                break;
            }

            $ids = array_map(function ($value) {
                return $value['_id'];
            }, $response['hits']['hits']);

            if (!empty($ids)) {
                $this->delete($ids, $index, $type);
            }

            if (!isset($response['_scroll_id'])) {
                break;
            }

            $params = [
                'scroll_id' => $response['_scroll_id'],
                'scroll' => '30s',
            ];


            $response = $this->_client->scroll($this->buildParams($params));
        }

        return $response;
    }

    /**
     * @param array $params
     * @return array
     */
    protected function buildParams(array $params = [])
    {
        return array_merge($this->getParams(), $params);
    }

    /**
     * Returns index name
     *
     * @param string $name
     * @param boolean $new
     * @param string $type
     * @return mixed|string
     */
    public function getIndexName($name, $new, $type)
    {
        $alias = strtolower($this->getIndexAlias($name, $type));
        // index name must be different than alias name
        $name = $alias . '_idx1';

        $indices = $this->getIndicesWithAlias($alias);

        if (!empty($indices)) {
            // Retrieve first indice because we should not have more than 1 indice per alias in our context
            $index = current($indices);
            if ($new) {
                $name = $index != $name ? $name : $alias . '_idx2';
            } else {
                $name = $index;
            }
        }

        return $name;
    }

    /**
     * Builds alias from given index name
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function getIndexAlias($name, $type)
    {
        return strtolower($this->_configHelper->getIndexPrefix($this->_scope) . $name . '_' . $type);
    }

    /**
     * Retrieves indices that belong to specified alias
     *
     * @param string $alias
     * @return array
     */
    public function getIndicesWithAlias($alias)
    {
        $indices = [];

        try {
            $params = ['name' => $alias];
            $aliasInfo = $this->_client->indices()->getAlias($this->buildParams($params));
            if (is_array($aliasInfo) && count($aliasInfo)) {
                $indices = array_keys($aliasInfo);
            }
        } catch (\Exception $e) {
            // Alias does not exist
            return [];
        }

        return $indices;
    }

    /**
     * @param string $indices
     * @param string $type
     * @param string $ids
     * @return mixed
     */
    public function getByIds($indices, $type, $ids)
    {
        $params['index'] = implode(',', (array)$indices);
        //$params['type'] = [$type];
        $params['body'] = ['ids' => $ids];

        return $this->_client->mget($this->buildParams($params));
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return [
            'client' => [
                'verify' => $this->_configHelper->isVerifyHost($this->_scope),
                'connect_timeout' => $this->_configHelper->getConnectTimeout($this->_scope),
            ]
        ];
    }
}