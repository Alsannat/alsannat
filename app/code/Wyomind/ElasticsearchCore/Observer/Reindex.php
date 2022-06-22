<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Observer;

class Reindex implements \Magento\Framework\Event\ObserverInterface
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
     * @var \Wyomind\ElasticsearchCore\Helper\ConfigFactory
     */
    protected $_configHelperFactory = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory;

    /**
     * Reindex class constructor
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\ConfigFactory $configHelperFactory
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\ConfigFactory $configHelperFactory,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
    )
    {
        $this->_eventManager = $eventManager;
        $this->_storeManager = $storeManager;
        $this->_configHelperFactory = $configHelperFactory;
        $this->_indexerHelperFactory = $indexerHelperFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            /** @var \Wyomind\ElasticsearchCore\Helper\Config $configHelper */
            $configHelper = $this->_configHelperFactory->create();
            /** @var \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper */
            $indexerHelper = $this->_indexerHelperFactory->create();
            $storeId = $this->_storeManager->getStore()->getId();

            $eventsList = $indexerHelper->getEventsList();
            $event = $observer->getEvent();
            $eventName = $event->getName();

            if (array_key_exists($eventName, $eventsList)) {
                foreach ($eventsList[$eventName] as $eventDetails) {
                    $type = $eventDetails['indexer'];
                    $action = $eventDetails['action'];
                    $eventObject = $observer->getDataObject();

                    // Check if the indexation is enabled
                    if ($configHelper->isIndexationEnabled($type, $storeId)) {
                        $indexer = $indexerHelper->getIndexer($type);

                        if (array_key_exists('getId', $eventDetails)) {
                            $objectId = $indexer->{$eventDetails['getId']}($observer);
                        } else {
                            $objectId = $eventObject->getId();
                        }
                        $indexer->$action($objectId);

                        if (array_key_exists('fallback', $eventDetails)) {
                            $this->_eventManager->dispatch($eventDetails['fallback'], ['id' => $objectId]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {

        }
    }
}