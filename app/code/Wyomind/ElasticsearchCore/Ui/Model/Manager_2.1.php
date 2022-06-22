<?php

namespace Wyomind\ElasticsearchCore\Ui\Model;


class Manager extends \Magento\Ui\Model\Manager
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
     * Manager constructor.
     * @param \Magento\Framework\View\Element\UiComponent\Config\Provider\Component\Definition $componentConfigProvider
     * @param \Magento\Framework\View\Element\UiComponent\Config\DomMergerInterface $domMerger
     * @param \Magento\Framework\View\Element\UiComponent\Config\ReaderFactory $readerFactory
     * @param \Magento\Framework\View\Element\UiComponent\ArrayObjectFactory $arrayObjectFactory
     * @param \Magento\Framework\View\Element\UiComponent\Config\FileCollector\AggregatedFileCollectorFactory $aggregatedFileCollectorFactory
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter
     * @param \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\Config\Provider\Component\Definition $componentConfigProvider,
        \Magento\Framework\View\Element\UiComponent\Config\DomMergerInterface $domMerger,
        \Magento\Framework\View\Element\UiComponent\Config\ReaderFactory $readerFactory,
        \Magento\Framework\View\Element\UiComponent\ArrayObjectFactory $arrayObjectFactory,
        \Magento\Framework\View\Element\UiComponent\Config\FileCollector\AggregatedFileCollectorFactory $aggregatedFileCollectorFactory,
        \Magento\Framework\Config\CacheInterface $cache,
        \Magento\Framework\Data\Argument\InterpreterInterface $argumentInterpreter,
        \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
    )
    {
        $construct = "__construct"; // in order to bypass the compiler
        parent::$construct($componentConfigProvider, $domMerger, $readerFactory, $arrayObjectFactory, $aggregatedFileCollectorFactory, $cache, $argumentInterpreter);
        $this->_sessionHelper = $sessionHelper;
        $this->_indexerHelper = $indexerHelper;
    }

    public function prepareData($name)
    {
        parent::prepareData($name);
        if ($name == 'elasticsearchcore_browse') {
            $data = $this->getData($name);

            list($type, $indice, $storeCode, $storeId) = $this->_sessionHelper->getBrowseData();

            if ($indice == null) {
                return $this;
            }

            $columns = &$data['elasticsearchcore_browse']['children']['columns']['children'];
            $indexer = $this->_indexerHelper->getIndexer($type);
            $columns = array_merge($columns, $indexer->getBrowseColumns($storeId));


            $this->componentsData->offsetSet($name, $data);
        }

        return $this;
    }
}