<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Ui\Component\Listing\Indexes\Column;

class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Actions constructor.
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Prepare Data Source
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['indexer_id'])) {
                    $item[$name]['flush'] = [
                        'href' => $this->_urlBuilder->getUrl(\Wyomind\ElasticsearchCore\Helper\Url::FLUSH_URL, ['indexer_id' => $item['indexer_id']]),
                        'label' => __('Flush'),
                        'confirm' => [
                            'title' => __('Flush'),
                            'message' => __("Are you sure you want to flush the index <b>%1</b> now?", $item['indexer_id'])
                        ]
                    ];
                    $item[$name]['run'] = [
                        'href' => $this->_urlBuilder->getUrl(\Wyomind\ElasticsearchCore\Helper\Url::RUN_URL, ['indexer_id' => $item['indexer_id']]),
                        'label' => __('Run'),
                        'confirm' => [
                            'title' => __('Run'),
                            'message' => __("Are you sure you want to run the index <b>%1</b> now?", $item['indexer_id'])
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}