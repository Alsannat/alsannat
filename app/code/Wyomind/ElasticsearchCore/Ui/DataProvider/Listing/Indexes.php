<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\DataProvider\Listing;

class Indexes extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Wyomind\ElasticsearchCore\Model\ResourceModel\Index\Collection
     */
    //protected $collection;

    /**
     * @var integer
     */
    protected $_size = 20;

    /**
     * @var integer
     */
    protected $_offset = 1;

    /**
     * @var array
     */
    protected $_likeFilters = [];

    /**
     * @var array
     */
    protected $_rangeFilters = [];

    /**
     * @var string
     */
    protected $_sortField = 'indexer_id';

    /**
     * @var string
     */
    protected $_sortDir = 'asc';

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer
     */
    protected $_indexerHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder
     */
    protected $_clientBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * Indexes constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder $clientBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder $clientBuilder,
        array $meta = [],
        array $data = []
    )
    {
        $this->_indexerHelper = $indexerHelper;
        $this->_configHelper = $configHelper;
        $this->_storeManager = $storeManager;
        $this->_clientBuilder = $clientBuilder;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $indexers = $this->_indexerHelper->getAllIndexers();

        $data = [];

        foreach ($indexers as $indexer) {
            $type = $indexer->getType();
            $tmp = [
                'id' => $type,
                'indexer_id' => $type,
                'updated_at' => $indexer->getUpdatedAt(),
                'last_index_date' => $indexer->getLastIndexDate(),
                'update_mode' => $indexer->getUpdateMode(),
                'reindexed' => $indexer->getReindexed(),
                'comment' => ''
            ];
            $indices = [];

            foreach ($this->_storeManager->getStores() as $store) {
                $storeCode = $store->getCode();
                $storeId = $store->getStoreId();
                $prefix = $this->_configHelper->getIndexPrefix($storeId);
                $hosts = $this->_configHelper->getServers($storeId);
                $indices[] = strtolower($prefix . $storeCode . "_" . $type . "_*");
            }

            $client = $this->_clientBuilder->create()->setHosts($hosts)->build();
            $stats = $client->indices()->stats(['index' => implode(',', $indices), 'metric' => ['docs', 'store'], 'client' => ['verify' => false]]);
            $tmp['comment'] = "<div class='elc-info'>" . $indexer->getComment();
            $tmp['comment'] .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" class="elc-more-info active">' . __('More information') . '</a><a href="#" class="elc-less-info">' . __("Less information") . '</a><ul class="indices-details"><br/>';

            foreach ($this->_storeManager->getStores() as $store) {
                $storeCode = strtolower($store->getCode());
                $storeId = $store->getStoreId();
                $active = $this->_configHelper->isIndexationEnabled($type, $storeId);
                $prefix = $this->_configHelper->getIndexPrefix($storeId);
                $indice = strtolower($prefix . $storeCode . '_' . $type);
                $stat = [];
                if (array_key_exists($indice . '_idx1', $stats['indices'])) {
                    $stat = $stats['indices'][$indice . '_idx1'];
                } else if (array_key_exists($indice . '_idx2', $stats['indices'])) {
                    $stat = $stats['indices'][$indice . '_idx2'];
                }

                $tmp['comment'] .= '<li class="" style="' . (!$active ? "color:lightgrey" : "") . '" title="' . (!$active ? 'This indexer is disabled' : '') . '">';
                $tmp['comment'] .= '<b><i>' . $indice . '</i></b>';

                if (!empty($stat)) {
                    $tmp['comment'] .= '<dd>' . $stat['total']['docs']['count'] . ' document' . ($stat['total']['docs']['count'] > 1 ? 's' : '') . '</dd>';
                    $tmp['comment'] .= '<dd>' . number_format($stat['total']['store']['size_in_bytes'] / 1024, 2, ',', ' ') . ' kB</dd>';
                } else {
                    $tmp['comment'] .= '<dd class="error">' . __('Not indexed') . '</dd>';
                }
                $tmp['comment'] .= '</li>';
            }
            $tmp['comment'] .= '</ul></div>';
            $data[] = $tmp;
        }

        $totalRecords = count($data);

        // sorting
        $sortField = $this->_sortField;
        $sortDir = $this->_sortDir;
        usort($data, function ($a, $b) use ($sortField, $sortDir) {
            if ($sortDir == 'asc') {
                return $a[$sortField] > $b[$sortField];
            } else {
                return $a[$sortField] < $b[$sortField];
            }
        });

        // filters
        foreach ($this->_likeFilters as $column => $value) {
            $data = array_filter($data, function ($item) use ($column, $value) {
                return stripos($item[$column], $value) !== false;
            });
        }

        // pagination
        $data = array_slice($data, ($this->_offset - 1) * $this->_size, $this->_size);

        return [
            'totalRecords' => $totalRecords,
            'items' => $data,
        ];
    }

    /**
     * Add filters to the collection
     * @param \Magento\Framework\Api\Filter $filter
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if ($filter->getConditionType() == 'like') {
            $this->_likeFilters[$filter->getField()] = substr($filter->getValue(), 1, -1);
        } elseif ($filter->getConditionType() == 'eq') {
            $this->_likeFilters[$filter->getField()] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'gteq') {
            $this->_rangeFilters[$filter->getField()]['from'] = $filter->getValue();
        } elseif ($filter->getConditionType() == 'lteq') {
            $this->_rangeFilters[$filter->getField()]['to'] = $filter->getValue();
        }
    }

    /**
     * Set the order of the collection
     * @param $field
     * @param $direction
     */
    public function addOrder($field, $direction)
    {
        $this->_sortField = $field;
        $this->_sortDir = strtolower($direction);
    }

    /**
     * Set the limit of the collection
     * @param $offset
     * @param $size
     */
    public function setLimit($offset, $size)
    {
        $this->_size = $size;
        $this->_offset = $offset;
    }
}
