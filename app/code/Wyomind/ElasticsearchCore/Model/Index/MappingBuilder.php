<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Index;

class MappingBuilder
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer
     */
    protected $_indexerHelperFactory = null;

    /**
     * MappingBuilder constructor.
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
    )
    {
        $this->_configHelper = $configHelper;
        $this->_indexerHelperFactory = $indexerHelperFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function build($storeId, $type)
    {
        $mapping = [];

        $indexer = $this->_indexerHelperFactory->create()->getIndexer($type);

        $compatibility = $this->_configHelper->getCompatibility($storeId);
        if ($compatibility >= 6 ) {
            $mapping[$type] = [
                'properties' => $indexer->getProperties($storeId)
            ];
        } elseif ($compatibility < 6) {
            $mapping[$type] = [
                '_all' => [
                    'analyzer' => $indexer->getLanguageAnalyzer($storeId)
                ],
                'properties' => $indexer->getProperties($storeId)
            ];
        }

        return $mapping;
    }
}