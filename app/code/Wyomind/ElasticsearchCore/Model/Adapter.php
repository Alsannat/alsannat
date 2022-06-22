<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model;

class Adapter
{
    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * @var Client
     */
    protected $_client = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Index\MappingBuilder
     */
    protected $_mappingBuilder = null;

    /**
     * Adapter constructor.
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param Client $client
     * @param Index\MappingBuilder $mappingBuilder
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        Index\MappingBuilder $mappingBuilder
    )
    {
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_configHelper = $configHelper;
        $this->_client = new Client($configHelper);
        $this->_mappingBuilder = $mappingBuilder;
    }


    /**
     * @param int $storeId
     * @param string $type
     * @return array
     */
    protected function getIndexParams($storeId, $type)
    {

        $compatibility = $this->_configHelper->getCompatibility($storeId);

        $settings = json_decode($this->_configHelper->getIndexSettings($storeId), true);
        $settings['index.mapping.total_fields.limit'] = 10000;

        // ELS 7 filter "standard" doesn't exist anymore + no more "custom"
        if ($compatibility == 7) {
            foreach ($settings['analysis']['analyzer'] as $analyzer => $data) {
                if (isset($data['filter'])) {
                    $newA = array_diff($data['filter'], ['standard']);
                    $settings['analysis']['analyzer'][$analyzer]['filter'] = $newA;
                }
            }
            $result = [
                'body' => [
                    'settings' => $settings,
                    'mappings' => $this->_mappingBuilder->build($storeId, $type)[$type]
                ]
            ];
        } else {
            $result = [
                //'custom' => ['update_all_types' => true],
                'body' => [
                    'settings' => $settings,
                    'mappings' => [
                        $type => $this->_mappingBuilder->build($storeId, $type)
                    ]
                ]
            ];
        }


        return $result;
    }

    /**
     * @param \Traversable $documents
     * @param string $type
     * @param boolean $full
     * @param int $storeId
     */
    public function addDocs(\Traversable $documents, $type, $full, $storeId)
    {
        // Initialize some variables
        $new = false;
        $this->_client->init($storeId);

        // Create a new index if full product reindexation is needed
        if ($full && $this->_configHelper->isSafeReindex($storeId)) {
            $new = true;
        }

        // Retrieve store index (create it if not exists)
        $index = $this->getIndex($storeId, $new, $type);

        // Index documents (this is a bulk indexation according to indexer batch size)
        foreach ($documents as $docs) {
            $this->_client->index($docs, $index, $type);
        }

        if ($new) {
            // Switch alias to the new index when indexation has ended
            $this->switchIndex($index, $storeId, $type);
        }
    }

    /**
     * @param string $type
     * @param int $storeId
     * @param array $ids
     */
    public function deleteDocs($type, $storeId, $ids)
    {
        $this->_client->init($storeId);
        $index = $this->getIndex($storeId, false, $type);
        $this->_client->delete($ids, $index, $type);
    }

    /**
     * @param string $index
     * @param int $storeId
     * @param string $type
     */
    protected function switchIndex($index, $storeId, $type)
    {
        $alias = $this->getIndexAlias($storeId, $type);
        $indices = $this->_client->getIndicesWithAlias($alias);
        foreach ($indices as $indexName) {
            if ($indexName != $index) {
                // remove old index that was linked to the alias
                $this->_client->deleteIndex($indexName);
            }
        }
        $this->_client->createAlias($index, $alias);
    }

    /**
     * @param int $storeId
     * @param bool $new
     * @param string $type
     * @return string
     */
    protected function getIndex($storeId, $new, $type)
    {
        $index = $this->getIndexName($storeId, $new, $type);

        // Delete index if exists and if we are indexing all documents
        $indexExists = $this->_client->existsIndex($index);

        if ($new && $indexExists) {
            $this->_client->deleteIndex($index);
            $indexExists = false;
        }

        // If index doesn't exist, create it
        if (!$indexExists) {
            $this->_eventManager->dispatch('wyomind_elasticsearchcore_create_index_before', ['index' => $index, 'store' => $storeId]);

            $this->_client->createIndex($index, $this->getIndexParams($storeId, $type));
            if (!$new) {
                $this->_client->createAlias($index, $this->getIndexAlias($storeId, $type));
            }

            $this->_eventManager->dispatch('wyomind_elasticsearchcore_create_index_after', ['index' => $index, 'store' => $storeId]);
        }

        return $index;
    }

    /**
     * @param int $storeId
     * @param boolean $new
     * @param string $type
     * @return string
     */
    protected function getIndexName($storeId, $new, $type)
    {
        $store = $this->_storeManager->getStore($storeId);

        return strtolower($this->_client->getIndexName($store->getCode(), $new, $type));
    }


    /**
     * @param int $storeId
     * @param string $type
     * @return string
     */
    protected function getIndexAlias($storeId, $type)
    {
        $store = $this->_storeManager->getStore($storeId);

        return strtolower($this->_client->getIndexAlias($store->getCode(), $type));
    }

    public function getConfigHelper()
    {
        return $this->_configHelper;
    }
}