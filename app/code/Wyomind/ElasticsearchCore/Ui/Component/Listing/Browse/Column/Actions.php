<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Listing\Browse\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder
     */
    protected $_urlBuilder;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    private $_indexerHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Session|null
     */
    private $_sessionHelper = null;

    /**
     * Actions constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper,
        \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_urlBuilder = $urlBuilder;
        $this->_indexerHelper = $indexerHelper;
        $this->_sessionHelper = $sessionHelper;
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        list($type, $indice, $store, $storeId) = $this->_sessionHelper->getBrowseData();

        if (isset($dataSource['data']['items'])) {
            $index = $this->_indexerHelper->getIndexer($type);

            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');

                $item[$name]['raw'] = [
                    'href' => "javascript:void(require(['elasticsearchcore_browse'], function (browse) { browse.raw('" .
                        $this->_urlBuilder->getUrl(
                            \Wyomind\ElasticsearchCore\Helper\Url::RAW_DATA_URL,
                            [
                                'indice' => $indice,
                                'store' => $store,
                                'storeId' => $storeId,
                                'type' => $type,
                                'id' => $item['id']
                            ]
                        ) . "'); }))",
                    'label' => __('Raw data'),
                    'hidden' => false,
                ];

                $index->getBrowseActions($item, $name);
            }
        }

        return $dataSource;
    }
}
