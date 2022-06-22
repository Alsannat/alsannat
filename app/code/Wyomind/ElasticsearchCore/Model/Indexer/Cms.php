<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Indexer;

class Cms extends AbstractIndexer
{
    /**
     * @var string
     */
    public $type = 'cms';

    /**
     * @var string
     */
    public $name = 'Cms Pages';

    /**
     * @var string
     */
    public $comment = 'CMS pages indexer';

    /**
     * @var array
     */
    protected $allowedTypes = [
        'char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext'
    ];

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page
     */
    protected $_pageResource = null;

    protected $_searchableAttributes = [];

    /**
     * @param \Wyomind\ElasticsearchCore\Model\Indexer\Context $context
     * @param \Magento\Cms\Model\ResourceModel\Page $pageResource
     */
    public function __construct(
        Context $context,
        \Magento\Cms\Model\ResourceModel\Page $pageResource
    )
    {
        parent::__construct($context);
        $this->_pageResource = $pageResource;
    }

    /**
     * {@inheritdoc}
     */
    public function export($storeId, $ids = [])
    {
        $this->handleLog('');
        $this->handleLog('<comment>' . __('Indexing cms pages for store id: ') . $storeId . '</comment>');

        $this->_eventManager->dispatch('wyomind_elasticsearchcore_cms_export_before', [
            'store_id' => $storeId,
            'ids' => $ids
        ]);

        $pages = [];
        $attributesConfig = [];
        $attributes = $this->_configHelper->getEntitySearchableAttributes($this->type, $storeId);

        foreach ($attributes as $attributeCode => $attributeInfo) {
            if ($attributeInfo['c'] == "1") {
                $attributesConfig[] = $attributeCode;
            }
        }

        $collection = $this->createPageCollection()->addStoreFilter($storeId);

        // Searchable attributes
        if (count(array_filter($attributesConfig)) != 0) {
            $collection->addFieldToSelect($attributesConfig);
        }

        // Specific documents
        if (false === empty($ids)) {
            $collection->addFieldToFilter('page_id', ['in' => $ids]);
        }

        // Excluded pages
        $excluded = explode(',', $this->_configHelper->getStoreConfig('wyomind_elasticsearchcore/types/cms/excluded_pages', $storeId));

        if ($excluded) {
            $collection->addFieldToFilter('page_id', ['nin' => $excluded]);
        }
        // Only active pages
        $collection->addFieldToFilter('is_active', \Magento\Cms\Model\Page::STATUS_ENABLED);

        $this->handleLog('<info>' . count($collection) . ' cms pages found</info>');
        /** @var \Magento\Cms\Model\Page $page */
        foreach ($collection as $page) {
            $page->setContent(html_entity_decode($page->getContent()));
            $pages[$page->getId()] = array_merge(
                ['id' => (int)$page->getId()], $page->toArray($attributesConfig)
            );

            if (isset($pages[$page->getId()]['title'])) {
                $pages[$page->getId()]['title_suggester'] = $pages[$page->getId()]['title'];
            }
        }
        $this->handleLog('<info>' . count($collection) . __(' cms pages indexed') . '</info>');

        yield $pages;

        $this->_eventManager->dispatch('wyomind_elasticsearchcore_cms_export_after', [
            'store_id' => $storeId,
            'ids' => $ids
        ]);
    }

    /**
     * @return Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected function createPageCollection()
    {
        return $this->_objectManager->create(\Magento\Cms\Model\ResourceModel\Page\Collection::class);
    }

    protected function getSearchableAttributes($store)
    {
        if (!isset($this->_searchableAttributes[$store])) {
            $this->_searchableAttributes[$store] = [];
            $atts = $this->_configHelper->getEntitySearchableAttributes($this->type, $store);

            foreach ($atts as $attributeCode => $attributeInfo) {
                if ($attributeInfo['c'] === "1") {
                    if ($attributeInfo['b'] == "varchar" || $attributeInfo['b'] == "text")
                        $attributeInfo['b'] = "string";
                    $this->_searchableAttributes[$store][$attributeCode] = $attributeInfo;
                }
            }
        }

        return $this->_searchableAttributes[$store];
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($store = null, $withBoost = false)
    {
        $data = [];
        $charType = 'text';


        $compatibility = $this->_adapter->getConfigHelper()->getCompatibility($store);
        $searchableAttributes = $this->getSearchableAttributes($store);

        foreach ($searchableAttributes as $attributeCode => $attributeInfo) {
            $field = $attributeCode;
            if ($compatibility >= 6) {
                $data[$field] = [
                    'type' => 'text',
                    'analyzer' => $this->getLanguageAnalyzer($store),
                    'copy_to' => 'all',
                ];
            } elseif ($compatibility < 6) {
                $data[$field] = [
                    'type' => 'string',
                    'analyzer' => $this->getLanguageAnalyzer($store),
                    'include_in_all' => true
                ];

                $charType = 'string';
            }


            $data[$field]['fields']['prefix'] = [
                'type' => $charType,
                'analyzer' => 'text_prefix',
                'search_analyzer' => 'std',
            ];
            $data[$field]['fields']['suffix'] = [
                'type' => $charType,
                'analyzer' => 'text_suffix',
                'search_analyzer' => 'std'
            ];


            if ($field == 'title') {
                $data[$field . '_suggester'] = [
                    'type' => 'completion',
                    'analyzer' => 'std',
                    'search_analyzer' => 'std'
                ];
            }
        }

        if ($compatibility >= 6) {
            $data['all'] = ['type' => 'text'];
        }

        $data['id'] = ['type' => 'long'];

        $properties = new \Magento\Framework\DataObject($data);

        $this->_eventManager->dispatch('wyomind_elasticsearchcore_cms_index_properties', [
            'indexer' => $this,
            'store' => $store,
            'properties' => $properties
        ]);

        return $properties->getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getDynamicConfigGroups()
    {
        $dynamicConfigFields['enable'] = [
            'id' => 'enable',
            'translate' => 'label comment',
            'type' => 'select',
            'sortOrder' => '10',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Enable CMS Index'),
            'source_model' => 'Magento\Config\Model\Config\Source\Yesno',
            'comment' => __('If enabled, CMS pages will be indexed in Elasticsearch.'),
            '_elementType' => 'field',
            'path' => 'wyomind_elasticsearchcore/types/cms',
        ];


        // Indexable attributes
        $dynamicConfigFields['attributes'] = [
            'id' => 'attributes',
            'translate' => 'label comment',
            'type' => 'hidden',
            'sortOrder' => '30',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => 'Attributes to index',
            '_elementType' => 'field',
            'frontend_model' => 'Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field\CmsPageAttributes',
            'path' => 'wyomind_elasticsearchcore/types/cms',
            'depends' => [
                'fields' => [
                    'enable' => [
                        'id' => 'wyomind_elasticsearchcore/types/cms/enable',
                        'value' => '1',
                        '_elementType' => 'field',
                        'dependPath' => [
                            0 => 'wyomind_elasticsearchcore',
                            1 => 'types',
                            2 => 'cms',
                            3 => 'enable'
                        ]
                    ]
                ]
            ]
        ];


        $dynamicConfigFields['excluded_pages'] = [
            'id' => 'excluded_pages',
            'translate' => 'label comment',
            'type' => 'multiselect',
            'sortOrder' => '60',
            'showInDefault' => '1',
            'showInWebsite' => '1',
            'showInStore' => '1',
            'label' => __('Excluded Pages'),
            'source_model' => 'Wyomind\ElasticsearchCore\Model\Indexer\Cms::excludedPages',
            'comment' => __('Selected CMS pages will be excluded from search.'),
            'depends' => [
                'fields' => [
                    'enable' => [
                        'id' => 'wyomind_elasticsearchcore/types/cms/enable',
                        'value' => '1',
                        '_elementType' => 'field',
                        'dependPath' => [
                            0 => 'wyomind_elasticsearchcore',
                            1 => 'types',
                            2 => 'cms',
                            3 => 'enable'
                        ]
                    ]
                ]
            ],
            '_elementType' => 'field',
            'path' => 'wyomind_elasticsearchcore/types/cms'
        ];

        $dynamicConfigGroups['cms'] = [
            'id' => 'cms',
            'translate' => 'label',
            'sortOrder' => '60',
            'showInDefault' => '1',
            'showInWebsite' => '0',
            'showInStore' => '1',
            'label' => 'CMS',
            'children' => $dynamicConfigFields,
            '_elementType' => 'group',
            'path' => 'wyomind_elasticsearchcore/types'
        ];

        return $dynamicConfigGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return [
            'cms_page_save_after' => [['indexer' => $this->type, 'action' => 'executeRow']],
            'cms_page_delete_after' => [['indexer' => $this->type, 'action' => 'deleteRow']],
            'wyomind_elasticsearchcore_full_reindex_after_cms' => [['indexer' => $this->type, 'action' => 'reindex']]
        ];
    }

    public function excludedPages()
    {
        $options = [];

        $collection = $this->createPageCollection();
        foreach ($collection as $page) {
            /** @var \Magento\Cms\Model\Page $page */
            $options[] = [
                'value' => $page->getId(),
                'label' => $page->getTitle(),
            ];
        }

        return $options;
    }


    public function getBrowseColumns($storeId)
    {
        $columns = parent::getBrowseColumns($storeId);
        if (isset($columns['content'])) {
            $columns['content']['arguments']['data']['config']['bodyTmpl'] = 'Wyomind_ElasticsearchCore/listing/browse/bightml';
        }
        return $columns;
    }

    public function getBrowseActions(&$item, $name)
    {
        $item[$name]['edit'] = [
            'href' => $this->_urlBuilder->getUrl(
                'cms/page/edit',
                ['page_id' => $item['id']]
            ),
            'label' => __('Edit')
        ];
    }
}
