<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Plugin\Ui\Config;

class Data
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Session|null
     */
    private $_sessionHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    private $_indexerHelper = null;

    /**
     * Data constructor.
     * @param \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
    )
    {
        $this->_sessionHelper = $sessionHelper;
        $this->_indexerHelper = $indexerHelper;
    }

    /**
     * @param $subject
     * @param $proceed
     * @param null $path
     * @param null $default
     * @return mixed
     */
    public function aroundGet($subject, $proceed, $path = null, $default = null)
    {
        $data = $proceed($path, $default);

        if ($path == 'elasticsearchcore_browse') {

            list($type, $indice, $storeCode, $storeId) = $this->_sessionHelper->getBrowseData();

            // is still no indice, no change
            if ($indice == null) {
                return $data;
            }

            $columns = &$data['children']['columns']['children'];
            $indexer = $this->_indexerHelper->getIndexer($type);
            $columns = array_merge($columns, $indexer->getBrowseColumns($storeId));
        }

        return $data;
    }
}