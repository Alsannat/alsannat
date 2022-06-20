<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchAutocomplete\Block\Search;

/**
 * Display the cms pages search results in the search results page
 * @package Wyomind\ElasticsearchAutocomplete\Block\Search
 */
class Cms extends \Magento\Framework\View\Element\Template
{


    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory = null;

    /**
     * @var \Wyomind\ElasticsearchAutocomplete\Helper\Config
     */
    protected $configHelper = null;
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Data
     */
    protected $dataHelper;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Search\Model\QueryFactory $queryFactory
     * @param \Wyomind\ElasticsearchAutocomplete\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Wyomind\ElasticsearchAutocomplete\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->queryFactory = $queryFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    public function getDataHelper() {
        return $this->dataHelper;
    }

    public function getConfigHelper() {
        return $this->configHelper;
    }


    /**
     * Get the pages list matching the search term
     * @param string storeCode the store code
     * @return array the list of cms pages
     */
    public function getPageCollection($storeCode)
    {
        try {
            $config = new \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config($storeCode);
            $config->getData();
        } catch (\Exception $e) {
            return [];
        }

        $client = new \Wyomind\ElasticsearchCore\Model\Client($config, new \Psr\Log\NullLogger());
        $client->init($storeCode);

        $cache = new \Wyomind\ElasticsearchCore\Helper\Cache\FileSystem();
        $synonymsHelper = new \Wyomind\ElasticsearchCore\Helper\Synonyms();
        $requester = new \Wyomind\ElasticsearchCore\Helper\Requester($client, $config, $cache, $synonymsHelper);

        $query = $this->queryFactory->get();
        $collection = $requester->searchByType($storeCode, "cms", $query->getQueryText(), $this->getLimit(), $this->configHelper->isHighlightEnabled());
        return $collection['docs'];
    }

    /**
     * Get the number of pages to display
     * @return int the number of pages to display
     */
    public function getLimit()
    {
        return $this->configHelper->getCmsPageSearchLimit();
    }
}
