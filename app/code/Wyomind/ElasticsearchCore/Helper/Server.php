<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Class CheckElasticsearchServerStatus
 */
class Server
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Log
     */
    protected $_logHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\JsonConfig
     */
    protected $_jsonConfigHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Client
     */
    protected $_client = null;

    /**
     * Class constructor
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Log $logHelper
     * @param \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
     * @param \Wyomind\ElasticsearchCore\Model\Client $client
     */
    public function __construct(
        Config $configHelper,
        Log $logHelper,
        JsonConfig $jsonConfigHelper,
        \Wyomind\ElasticsearchCore\Model\Client $client
    )
    {
        $this->_configHelper = $configHelper;
        $this->_logHelper = $logHelper;
        $this->_jsonConfigHelper = $jsonConfigHelper;
        $this->_client = $client;
    }

    public function updateServerCron($storeId = '0', $code = 'default')
    {
        return $this->updateServer($storeId,$code,true);
    }

    /**
     * Update the server status, version and compatibility
     * @param string $storeId
     * @param string $code
     * @return string
     */
    public function updateServer($storeId = '0', $code = 'default', $isCron = false)
    {
        $output = '';
        $serverVersion = '';
        $compatibility = 6;
        $serverStatus = 0;

        $this->_client->init($storeId);

        $cleanCache = false;

        try {
            $info = $this->_client->info(['client' => ['verify' => false, 'connect_timeout' => 5]]);

            $versionArray = explode('.', $info['version']['number']);
            $version = array_shift($versionArray);
            $serverVersion = $info['version']['number'];

            if (in_array($version, [2, 5, 6, 7])) {
                $compatibility = $version;
//                if ($compatibility == 5) {
//                    $compatibility = 6;
//                }
                $serverStatus = 1;
                $message = __('Elasticsearch server version found: ') . $serverVersion;
                $output .= "<comment>" . $message . "</comment>";
            } else {
                $message = __('Elasticsearch server version found not compatible: ') . $serverVersion;
                $output .= "<error>" . $message . "</error>";
            }
        } catch (\Exception $e) {
            $message = __('Cannot find the Elasticsearch server version: ') . $e->getMessage();
            $output .= "<error>" . $message . "</error>";
        }

        $this->_logHelper->serverLog($storeId, $message, $serverStatus, $serverVersion, $compatibility);

        $oldServerVersion = $this->_configHelper->getServerVersion($storeId);

        if ($oldServerVersion != $serverVersion) {
            $this->_configHelper->setServerVersion($serverVersion, $storeId, false);
            $cleanCache = true;
        }

        $oldCompatibility = $this->_configHelper->getCompatibility($storeId);
        if ($oldCompatibility != $compatibility) {
            $this->_configHelper->setCompatibility($compatibility, $storeId, false);
            $cleanCache = true;
        }

        $oldServerStatus = $this->_configHelper->getServerStatus($storeId);
        if ($oldServerStatus != $serverStatus) {
            $this->_configHelper->setServerStatus($serverStatus, $storeId, false);
            $cleanCache = true;
        }

        if ($oldServerStatus != $serverStatus) {
            $cleanCache |= $this->_configHelper->updateCanUseModulesInLayout([], $storeId, false);
        }

        if (!$isCron && $cleanCache) {
            $this->_configHelper->cleanCache();
        }

        $jsonConfigParameters = [
            'server_version' => $serverVersion,
            'compatibility' => $compatibility,
            'elasticsearch_server_status' => $serverStatus
        ];

        $this->_jsonConfigHelper->saveConfig($code, $jsonConfigParameters);

        if ($isCron) {
            return $cleanCache;
        }
        return $output;
    }
}