<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class Indexer
{
    const UPDATE_MODE_ON_SAVE = 0;
    const UPDATE_MODE_BY_SCHEDULE = 1;
    const ACTIVE = 1;
    const UNACTIVE = 0;

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_directoryReader = null;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;
    
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\IndexFactory
     */
    protected $_indexModelFactory = null;

    /**
     * @var Config|null
     */
    protected $_configHelper = null;

    /**
     * Class constructor
     * @param \Magento\Framework\Module\Dir\Reader $directoryReader
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Model\IndexFactory $indexModelFactory
     * @param Config $configHelper
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $directoryReader,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Model\IndexFactory $indexModelFactory,
        Config $configHelper
    )
    {
        $this->_directoryReader = $directoryReader;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_indexModelFactory = $indexModelFactory;
        $this->_configHelper = $configHelper;
    }

    /**
     * Indexer classes list
     * @return array
     */
    public function getClassList()
    {
        $controllerModule = 'Wyomind_ElasticsearchCore';
        $directory = $this->_directoryReader->getModuleDir('', $controllerModule) . '/Model/Indexer';
        $classList = array_diff(scandir($directory), ['..', '.', 'AbstractIndexer.php', 'Context.php']);

        return $classList;
    }

    /**
     * Get one specific indexer
     * @param string $type
     * @return object
     */
    public function getIndexer($type)
    {
        $indexers = $this->getAllIndexers();

        return array_key_exists($type, $indexers) ? $indexers[$type] : null;
    }


    /**
     * Get the list of all indexers
     * @return array
     */
    public function getAllIndexers()
    {
        $indexers = [];
        $classList = $this->getClassList();

        foreach ($classList as $class) {
            $className = pathinfo($class, PATHINFO_FILENAME);
            $path = '\Wyomind\ElasticsearchCore\Model\Indexer\\' . $className;
            $model = $this->_objectManager->get($path);

            /** @var \Wyomind\ElasticsearchCore\Model\Index $index */
            $index = $this->_indexModelFactory->create()->loadByIndexerId($model->type);
            if ($index->getLastIndexDate() !== "0000-00-00 00:00:00") {
                $model->setLastIndexDate($index->getLastIndexDate());
            }

            $model->setReindexed($index->getReindexed());

            $indexers[$model->type] = $model;
        }

        return $indexers;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getIndices($type)
    {
        $indices = [];
        /** @var \Magento\Store\Api\Data\StoreInterface $store */
        foreach ($this->_storeManager->getStores() as $store) {
            $active = $this->_configHelper->isIndexationEnabled($type, $store->getStoreId());
            if (!$active) {
                continue;
            }
            $storeCode = $store->getCode();
            $prefix = $this->_configHelper->getIndexPrefix($store->getStoreId());
            $indices[] = [
                'type' => $type,
                'indice' => strtolower($prefix . $storeCode . '_' . $type),
                'storeCode' => $storeCode,
                'storeId' => $store->getId()
            ];
        }

        return $indices;
    }

    /**
     * @return array|mixed
     */
    public function getFirstIndice()
    {
        $indexers = $this->getAllIndexers();
        foreach ($indexers as $indexer) {
            $indices = $this->getIndices($indexer->getType());
            if (count($indices) > 0) {
                return $indices[0];
            }
        }
        
        return [
            'type' => null,
            'indice' => null,
            'storeCode' => null,
            'storeId' => null
        ];
    }

    /**
     * Get the list of indexers types
     * @return array
     */
    public function getTypes()
    {
        $types = [];
        $classList = $this->getClassList();

        foreach ($classList as $class) {
            $className = pathinfo($class, PATHINFO_FILENAME);
            $path = '\Wyomind\ElasticsearchCore\Model\Indexer\\' . $className;
            $model = $this->_objectManager->get($path);
            $types[$model->type] = $model->type;
        }

        return $types;
    }

    /**
     * Build the Index Settings section in system.xml according to the indexer list
     * @return array
     */
    public function getDynamicTypes()
    {
        $section = [];
        $dynamicGroups = [];
        $indexers = $this->getAllIndexers();

        foreach ($indexers as $indexer) {
            $dynamicGroups += $indexer->getDynamicConfigGroups();
        }

        if (!empty($dynamicGroups)) {
            $section = [
                'id' => 'types',
                'translate' => 'label',
                'showInDefault' => '1',
                'showInWebsite' => '0',
                'showInStore' => '1',
                'sortOrder' => '9998',
                'label' => 'Indexes Settings',
                'children' => $dynamicGroups,
                '_elementType' => 'group',
                'path' => 'wyomind_elasticsearchcore'
            ];
        }

        return $section;
    }
    
    /**
     * Get the dynamic list of events that are defined in events.xml
     * @return array
     */
    public function getEventsList()
    {
        $dynamicEvents = [];
        $indexers = $this->getAllIndexers();

        foreach ($indexers as $indexer) {
            $dynamicEvents = array_merge_recursive($dynamicEvents, $indexer->getEvents());
        }
        
        return $dynamicEvents;
    }
}