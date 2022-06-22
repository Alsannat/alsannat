<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper\Autocomplete;

/**
 * Class Config
 */
class Config implements \Wyomind\ElasticsearchCore\Helper\ConfigInterface
{
    /**
     * @var array
     */
    private $_data = [];
    /**
     * @var string
     */
    private $_file = '';
    /**
     * @var string
     */
    private $_storeCode = '';

    /**
     * Config constructor.
     * @param string $storeCode
     */
    public function __construct($storeCode)
    {
        $this->_storeCode = $storeCode;
        $path = BP . '/var/wyomind/elasticsearch/';
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        $this->_file = $path . $storeCode . '.json';
    }

    /**
     * @return array|mixed
     * @throws \Exception
     */
    public function getData()
    {
        if (count($this->_data) == 0) {
            if (!file_exists($this->_file) || !is_file($this->_file) || !filesize($this->_file)) {
                throw new \Exception(sprintf('Could not find config file for scope "%s"', $this->_storeCode));
            }
            try {
                $this->_data = json_decode(file_get_contents($this->_file), true);
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Invalid config file for scope "%s"', $this->_storeCode));
            }
        }
        return $this->_data;
    }

    /**
     * @param $data
     * @return bool|int
     */
    public function setData($data)
    {
        $folder = dirname($this->_file);
        if (!is_dir($folder)) {
            try {
                mkdir($folder, 0777, true);
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Cannot create the folder to store the elasticsearch configuration'));
            }
        }

        return file_put_contents($this->_file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * @param null $scopeId
     * @return array|mixed
     */
    public function getCompatibility($scopeId = null)
    {
        return $this->getValue('compatibility');
    }

    /**
     * @param null $scopeId
     * @return array|mixed
     */
    public function getTypes($scopeId = null)
    {
        return $this->getValue('types');
    }

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getQueryOperator($storeId = null)
    {
        return $this->getValue('query_operator');
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isFuzzyQueryEnabled($storeId = null)
    {
        return $this->getValue('enable_fuzzy_query') === '1';
    }

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getFuzzyQueryMode($storeId = null)
    {
        return $this->getValue('fuzzy_query_mode');
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isProductWeightEnabled($storeId = null)
    {
        return $this->getValue('enable_product_weight') === '1';
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function getServers($storeId = null)
    {
        return explode(',', $this->getValue('servers'));
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isVerifyHost($storeId = null)
    {
        return $this->getValue('verify_host') === '1';
    }

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getConnectTimeout($storeId = null)
    {
        return $this->getValue('timeout');
    }

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getCategoryTree($storeId = null)
    {
        return $this->getValue('categories');
    }

    /**
     * @param null $storeId
     * @return array|mixed
     */
    public function getIndexPrefix($storeId = null)
    {
        return $this->getValue('index_prefix');
    }

    public function isFrontendLogEnabled($storeId = null)
    {
        return $this->getValue('enable_frontend_request_log');
    }

    /**
     * @param $key
     * @param null $scopeId
     * @return array|mixed
     */
    public function getValue($key, $scopeId = null)
    {
        if (empty($this->_data)) {
            $this->getData();
        }
        $keys = explode('/', $key);
        $result = $this->_data;
        foreach ($keys as $key) {
            $result = $result[$key];
        }
        
        return $result;
    }
}