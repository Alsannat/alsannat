<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchAutocomplete\Helper;

/**
 * Class Config
 * @package \Wyomind\ElasticsearchAutocomplete\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Did you mean enabled?
     */
    const XML_PATH_DIDYOUMEAN_ENABLE_SEARCH = "wyomind_elasticsearchautocomplete/settings/didyoumean/enable_search";
    /**
     * Did you mean enabled in autocomplete?
     */
    const XML_PATH_DIDYOUMEAN_ENABLE_AUTOCOMPLETE = "wyomind_elasticsearchautocomplete/settings/didyoumean/enable_autocomplete";
    /**
     * CMS pages search limit
     */
    const XML_PATH_CMS_SEARCH_LIMIT = "wyomind_elasticsearchautocomplete/settings/cms/search_limit";
    /**
     * Categories search limit
     */
    const XML_PATH_CATEGORY_SEARCH_LIMIT = "wyomind_elasticsearchautocomplete/settings/category/search_limit";
    /**
     * Highlight enabled?
     */
    const XML_PATH_ENABLE_HIGHLIGHT = "wyomind_elasticsearchautocomplete/settings/general/enable_highlight";


    const XML_PATH_LABELS = "wyomind_elasticsearchautocomplete/settings/general/labels";

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig = null;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    ) {

        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
    }


    public function getLabels($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_LABELS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Is the did you mean feature enabled?
     * @param int|null $store the store id
     * @return int 0 is not enabled, 1 if enabled
     */
    public function isDidYouMeanEnableSearch($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_DIDYOUMEAN_ENABLE_SEARCH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Is the did you mean feature enabled in the autocomplete ?
     * @param int|null $store the store id
     * @return int 0 is not enabled, 1 if enabled
     */
    public function isDidYouMeanEnableAutocomplete($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_DIDYOUMEAN_ENABLE_AUTOCOMPLETE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get the configuration for ElasticsearchAutocomplete
     * @param int|null $store the store id
     * @return mixed the elasticsearchautocomplete config
     */
    public function getConfig($store = null)
    {
        return $this->_scopeConfig->getValue("wyomind_elasticsearchautocomplete/settings", \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get the number of pages to display in the search results page
     * @param int|null $store the store id
     * @return int the number of pages to display
     */
    public function getCmsPageSearchLimit($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_CMS_SEARCH_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Get the number of categories to display in the search results page
     * @param int|null $store the store id
     * @return int the number of categories to display
     */
    public function getCategoryPageSearchLimit($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_CATEGORY_SEARCH_LIMIT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Is highlighting the search terms in results enabled?
     * @param int|null $store the store id
     * @return int 0 is not enabled, 1 if enabled
     */
    public function isHighlightEnabled($store = null)
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ENABLE_HIGHLIGHT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }
}
