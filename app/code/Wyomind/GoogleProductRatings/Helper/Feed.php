<?php

/**
 * Copyright ©2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Helper;

/**
 *
 * @exclude_var e,ex
 */
class Feed extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Wyomind\GoogleProductRatings\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewCollectionFactory;
    /**
     * @var boolean
     */
    private $_logEnabled = false;
    /**
     * @var string
     */
    private $_url = null;
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind, \Magento\Framework\App\Helper\Context $context, \Wyomind\GoogleProductRatings\Model\ResourceModel\Review\CollectionFactory $_reviewCollectionFactory)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context);
        $this->_reviewCollectionFactory = $_reviewCollectionFactory;
    }
    /**
     * Generate reviews data feed
     *
     * @param array $scopeIds
     * @return mixed
     * @throws \Exception
     */
    public function generate($scopeIds)
    {
        try {
            // Check if log is enables
            $this->checkLogFlag();
            $this->__log("*********************************************************************");
            $this->__log("**************************** NEW PROCESS ****************************");
            $this->__log("*********************************************************************");
            // Get scope information
            $this->__log(">> Get scope information");
            $this->_scopeHelper->initScope($scopeIds);
            $storeManager = $this->_scopeHelper->getStoreManager();
            $directory = $this->_scopeHelper->getDirectory();
            $scope = $this->_scopeHelper->getScope();
            $scopeId = $this->_scopeHelper->getScopeId();
            $storeIds = $this->_scopeHelper->getStoreIds();
            $this->__log(">> Scope: '" . $scope . "', ID: '" . $scopeId . "'");
            // Url
            $this->_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            // Get the xml pattern
            $this->__log(">> XML pattern");
            $xml = $this->getXmlPattern($storeIds, $scope, $scopeId);
            // Generate / Save File
            $this->__log(">> Generate / Save file");
            $configFileName = $this->scopeConfig->getValue(Config::XML_PATH_STORAGE_FILE_NAME, $scope, $scopeId);
            $configFilePath = $this->scopeConfig->getValue(Config::XML_PATH_STORAGE_FILE_PATH, $scope, $scopeId);
            $fileName = $configFileName . '.xml';
            $filePath = $this->_storageHelper->getAbsoluteRootDir() . DIRECTORY_SEPARATOR . $configFilePath . $directory;
            $this->__log(">> File path: " . $filePath . $fileName);
            $this->_storageHelper->mkdir($filePath);
            $file = $this->_storageHelper->fileOpen($filePath, $fileName);
            $this->_storageHelper->fileWrite($file, $xml);
            $this->_storageHelper->fileClose($file);
            // Link
            $this->__log(">> Generate link");
            $data['link'] = $this->_url . $configFilePath . $directory . DIRECTORY_SEPARATOR . $fileName;
            $this->__log(">> " . $data['link']);
            $gmtTimestamp = $this->_coreDate->gmtTimestamp();
            $gmtOffset = $this->_coreDate->getGmtOffset();
            $data['updated_at'] = __("Last update: ") . $this->_coreDate->date('d M Y H:i:s', $gmtTimestamp + $gmtOffset);
            // Save config
            $this->__log(">> Save config");
            $this->_configWriter->save(Config::XML_PATH_STORAGE_UPDATED_AT, $gmtTimestamp, $scope, $scopeId);
            $this->__log(">> Generation complete");
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
     * Get the XML pttern
     *
     * @param array $storeIds
     * @param string $scope
     * @param int $scopeId
     * @return string
     */
    private function getXmlPattern($storeIds, $scope, $scopeId)
    {
        // Get reviews
        $this->__log(">> Get reviews");
        $reviews = $this->_reviewCollectionFactory->create()->getReviews($storeIds);
        $counter = 0;
        // Data feed settings
        // Publisher name
        $publisherName = $this->scopeConfig->getValue(Config::XML_PATH_DATA_FEED_SETTINGS_PUBLISHER_NAME, $scope, $scopeId);
        // Collection method
        $collectionMethod = $this->scopeConfig->getValue(Config::XML_PATH_DATA_FEED_SETTINGS_COLLECTION_METHOD, $scope, $scopeId);
        // Rating range min
        $ratingRangeMin = $this->scopeConfig->getValue(Config::XML_PATH_DATA_FEED_SETTINGS_RATING_RANGE_MIN, $scope, $scopeId);
        // Rating range max
        $ratingRangeMax = $this->scopeConfig->getValue(Config::XML_PATH_DATA_FEED_SETTINGS_RATING_RANGE_MAX, $scope, $scopeId);
        // Anonymous reviewer
        $anonymousReviewer = $this->scopeConfig->getValue(Config::XML_PATH_DATA_FEED_SETTINGS_ANONYMOUS_REVIEWER, $scope, $scopeId);
        // Product settings
        // Use minimal configuration
        $minimalConfiguration = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_USE_MINIMAL_CONFIGURATION, $scope, $scopeId);
        $this->__log(">> Minimal configuration: " . $minimalConfiguration);
        // GTIN
        $gtinAttribute = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_GTIN, $scope, $scopeId);
        // MPN
        $mpnAttribute = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_MPN, $scope, $scopeId);
        // SKU
        $skuAttribute = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_SKU, $scope, $scopeId);
        // Brand
        $brandAttribute = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_BRAND, $scope, $scopeId);
        // Name
        $nameAttribute = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_PRODUCT_NAME, $scope, $scopeId);
        // Check if out of stock products should be filtered
        $filterOutofstock = $this->scopeConfig->getValue(Config::XML_PATH_OPTIONS_PRODUCT_SETTINGS_FILTER_UNAVAILABLE, $scope, $scopeId);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "
" . '<feed xmlns:vc="http://www.w3.org/2007/XMLSchema-versioning" ' . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' . 'xsi:noNamespaceSchemaLocation="http://www.google.com/shopping/reviews/schema/product/2.1/product_reviews.xsd">' . "
" . '    <publisher>' . "
" . '        <name><![CDATA[' . $publisherName . ']]></name>' . "
" . '        <favicon><![CDATA[' . $this->_url . 'favicon.png]]></favicon>' . "
" . '    </publisher>' . "
" . '    <reviews>' . "
";
        foreach ($reviews as $review) {
            if ($review['score'] != '') {
                if ($review['customer_id']) {
                    $reviewerId = $review['customer_id'];
                } else {
                    $reviewerId = $review['detail_id'] . '-' . $review['review_id'];
                }
                $isAnonymous = 'true';
                $nickname = 'Anonymous';
                if ('1' !== $anonymousReviewer) {
                    if ($review['nickname'] != '') {
                        $isAnonymous = 'false';
                        $reviewerName = explode(' ', $review['nickname'], 2);
                        // To Export only the first string before the first space
                        $nickname = $reviewerName[0];
                    }
                }
                $createdAt = $this->_coreDate->date("Y-m-d\\Th:i:s\\Z", strtotime($review['created_at']));
                $reviewUrl = $this->_url . 'review/product/view/id/' . $review['review_id'] . '/product_id/' . $review['entity_pk_value'] . '/?___store=' . $scopeId;
                $product = null;
                try {
                    $product = $this->_productRepository->getById($review['entity_pk_value'], false, $scopeId);
                } catch (\Exception $ex) {
                    $product = null;
                }
                if ($product == null) {
                    continue;
                }
                // Only export the product if it is visible and active
                if ($product->getVisibility() == 1 || $product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED) {
                    continue;
                }
                // Only export the product if it is available => it can be bought
                if ($filterOutofstock && !$product->isAvailable()) {
                    continue;
                }
                $productUrl = str_replace(" ", "%20", $product->getProductUrl());
                $name = $product->getName();
                $xml .= '        <review>' . "
" . '            <review_id><![CDATA[' . $review['review_id'] . ']]></review_id>' . "
" . '            <reviewer>' . "
" . '                <name is_anonymous="' . $isAnonymous . '"><![CDATA[' . $nickname . ']]></name>' . "
" . '                <reviewer_id><![CDATA[' . $reviewerId . ']]></reviewer_id>' . "
" . '            </reviewer>' . "
" . '            <review_timestamp><![CDATA[' . $createdAt . ']]></review_timestamp>' . "
" . '            <title><![CDATA[' . $review['title'] . ']]></title>' . "
" . '            <content><![CDATA[' . $review['detail'] . ']]></content>' . "
" . '            <review_url type="singleton"><![CDATA[' . $reviewUrl . ']]></review_url>' . "
" . '            <ratings>' . "
" . '                <overall min="' . $ratingRangeMin . '" max="' . $ratingRangeMax . '"><![CDATA[' . $review['score'] . ']]></overall>' . "
" . '            </ratings>' . "
" . '            <products>' . "
" . '                <product>' . "
";
                if (0 == $minimalConfiguration) {
                    if ($product->getData($nameAttribute)) {
                        $name = $product->getData($nameAttribute);
                    }
                    $xml .= '                    <product_ids>' . "
";
                    if ($gtinAttribute) {
                        $gtin = $product->getData($gtinAttribute);
                        if ($gtin) {
                            $xml .= '                        <gtins>' . "
" . '                            <gtin><![CDATA[' . $gtin . ']]></gtin>' . "
";
                            // loop over children product for configurable product
                            if ($product->getTypeId() == "configurable") {
                                $childProducts = $product->getTypeInstance()->getUsedProducts($product);
                                foreach ($childProducts as $child) {
                                    $xml .= '                            <gtin><![CDATA[' . $child->getData($gtinAttribute) . ']]></gtin>' . "
";
                                }
                            }
                            $xml .= '                        </gtins>' . "
";
                        }
                    }
                    if ($mpnAttribute) {
                        $mpn = $product->getData($mpnAttribute);
                        if ($mpn) {
                            $xml .= '                        <mpns>' . "
" . '                            <mpn><![CDATA[' . $mpn . ']]></mpn>' . "
";
                            // loop over children product for configurable product
                            if ($product->getTypeId() == "configurable") {
                                $childProducts = $product->getTypeInstance()->getUsedProducts($product);
                                foreach ($childProducts as $child) {
                                    $child = $child->load($child->getId());
                                    $xml .= '                            <mpn><![CDATA[' . $child->getData($mpnAttribute) . ']]></mpn>' . "
";
                                }
                            }
                            $xml .= '                        </mpns>' . "
";
                        }
                    }
                    if ($skuAttribute) {
                        $sku = $product->getData($skuAttribute);
                        if ($sku) {
                            $xml .= '                        <skus>' . "
" . '                            <sku><![CDATA[' . $sku . ']]></sku>' . "
";
                            // loop over children product for configurable product
                            if ($product->getTypeId() == "configurable") {
                                $childProducts = $product->getTypeInstance()->getUsedProducts($product);
                                foreach ($childProducts as $child) {
                                    $xml .= '                            <sku><![CDATA[' . $child->getSku() . ']]></sku>' . "
";
                                }
                            }
                            $xml .= '                        </skus>' . "
";
                        }
                    }
                    if ($brandAttribute) {
                        try {
                            $brandText = $product->getAttributeText($brandAttribute);
                            $brandValue = $product->getData($brandAttribute);
                            $brand = is_numeric($brandValue) ? $brandText : $brandValue;
                            if ($brand) {
                                $xml .= '                        <brands>' . "
" . '                            <brand><![CDATA[' . $brand . ']]></brand>' . "
";
                                // loop over children product for configurable product
                                if ($product->getTypeId() == "configurable") {
                                    $childProducts = $product->getTypeInstance()->getUsedProducts($product);
                                    foreach ($childProducts as $child) {
                                        $xml .= '                            <brand><![CDATA[' . $brand . ']]></brand>' . "
";
                                    }
                                }
                                $xml .= '                        </brands>' . "
";
                            }
                        } catch (\Throwable $e) {
                        }
                    }
                    $xml .= '                    </product_ids>' . "
";
                }
                $xml .= '                    <product_name><![CDATA[' . $name . ']]></product_name>' . "
" . '                    <product_url><![CDATA[' . $productUrl . ']]></product_url>' . "
" . '                </product>' . "
";
                $xml .= '            </products>' . "
" . '            <is_spam>false</is_spam>' . "
" . '            <collection_method><![CDATA[' . $collectionMethod . ']]></collection_method>' . "
" . '        </review>' . "
";
                $counter++;
            }
        }
        $xml .= '    </reviews>' . "
" . '</feed>';
        $this->__log(">> Total reviews exported: " . $counter);
        return $xml;
    }
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
    /*                           DEBUG UTILITIES                              */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~*/
    /**
     * Check if the log reporting is enabled
     */
    private function checkLogFlag()
    {
        if (!$this->_logEnabled) {
            $this->_logEnabled = $this->scopeConfig->getValue(Config::XML_PATH_SETTINGS_LOG) ? true : false;
        }
    }
    /**
     * Add a message to the log file
     * @param string $msg
     */
    public function __log($msg)
    {
        if ($this->_logEnabled) {
            $this->_logger->notice($msg);
        }
    }
}