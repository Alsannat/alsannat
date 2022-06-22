<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchAutocomplete\Block\Search;

/**
 * Display the first suggestion in search results page
 * @package Wyomind\ElasticsearchAutocomplete\Block\Search
 */
class DidYouMean extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory = null;

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $searchDataHelper = null;

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
     * @param \Magento\Search\Helper\Data $searchDataHelper
     * @param \Wyomind\ElasticsearchAutocomplete\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Magento\Search\Helper\Data $searchDataHelper,
        \Wyomind\ElasticsearchAutocomplete\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->queryFactory = $queryFactory;
        $this->searchDataHelper = $searchDataHelper;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    public function getDataHelper() {
        return $this->dataHelper;
    }

    /**
     * Get the suggestion term matching the search term
     * @param string $storeCode code of the store
     * @return string the suggestion
     */
    public function getSuggestion($storeCode)
    {
        try {
            $config = new \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config($storeCode);
            $config->getData();
        } catch (\Exception $e) {
            return "";
        }


        $client = new \Wyomind\ElasticsearchCore\Model\Client($config, new \Psr\Log\NullLogger());
        $client->init($storeCode);

        $cache = new \Wyomind\ElasticsearchCore\Helper\Cache\FileSystem();
        $synonymsHelper = new \Wyomind\ElasticsearchCore\Helper\Synonyms();
        $requester = new \Wyomind\ElasticsearchCore\Helper\Requester($client, $config, $cache, $synonymsHelper);

        $query = $this->queryFactory->get();
        $suggests = $requester->getSuggestions($storeCode, $query->getQueryText(), 1)['docs'];

        if (!empty($suggests)) {
            $suggest = array_pop($suggests);
            return $suggest['text'];
        } else {
            return "";
        }
    }

    /**
     * Get the search url for the suggest term
     * @param string $suggestion the suggest term
     * @return string the url
     */
    public function getQueryUrl($suggestion)
    {
        return $this->searchDataHelper->getResultUrl() . "?q=" . $suggestion;
    }

    /**
     * Are suggestions enabled?
     * @return boolean true if enabled, else false
     */
    public function isSuggestionEnabled()
    {
        return $this->configHelper->isDidYouMeanEnableSearch();
    }
}
