<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Plugin\Config\Model;

class Config
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    public $messageManager = null;

    /**
     * @var StoreManagerInterface|\Magento\Store\Model\StoreManagerInterface\Proxy|null
     */
    public $storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config|null
     */
    public $configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Log
     */
    protected $_logHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Data
     */
    protected $_dataHelper = null;

    /**
     * Config constructor
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Log $logHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Wyomind\ElasticsearchCore\Helper\Log $logHelper,
        \Wyomind\ElasticsearchCore\Helper\Data $dataHelper
    )
    {
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->configHelper = $configHelper;
        $this->_logHelper = $logHelper;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Check the value of the configuration before saving them
     * @param \Magento\Config\Model\Config $subject
     */
    public function beforeSave($subject)
    {
        $groups = $subject->getGroups();
        $storeId = $subject->getStore();
        if ($storeId == null) {
            $storeId = 0;
        }

        if ($groups != null) {
            foreach ($groups as $groupId => $groupData) {
                $groupPath = $subject->getSection() . '/' . $groupId;

                if ($groupPath === 'wyomind_elasticsearchcore/configuration') {
                    if (isset($groupData['fields']['servers']['value'])) {
                        // automatically retrieve the ES server version
                        $hosts = explode(',', $groupData['fields']['servers']['value']);
                        foreach ($hosts as $host) {
                            $serverVersion = '';
                            $compatibility = 6;
                            $serverStatus = 0;

                            $client = \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder::create()->setHosts([$host])->build();

                            try {
                                $info = $client->info(['client' => ['verify' => false, 'connect_timeout' => 5]]);
                                $versionArray = explode('.', $info['version']['number']);
                                $version = array_shift($versionArray);
                                $serverVersion = $info['version']['number'];

                                if (in_array($version, [2, 5, 6, 7])) {
                                    $compatibility = $version;
                                    $serverStatus = 1;
                                    $message = __('Elasticsearch server version found: ') . $serverVersion;
                                    $this->messageManager->addSuccess($message);
                                } else {
                                    $message = __('Elasticsearch server version found not compatible: ') . $serverVersion;
                                    $this->messageManager->addError($message);
                                }
                            } catch (\Exception $e) {
                                $message = __('Cannot find the Elasticsearch server version: ') . $e->getMessage();
                                $this->messageManager->addError($message);
                            }

                            $groups[$groupId]['fields']['server_version']['value'] = $serverVersion;
                            $groups[$groupId]['fields']['compatibility']['value'] = $compatibility;
                            $groups[$groupId]['fields']['elasticsearch_server_status']['value'] = $serverStatus;
                            $subject->setGroups($groups);
                        }
                    }
                }
            }
        }
    }

    public function afterSave($subject)
    {
        $groups = $subject->getGroups();

        if ($groups != null) {
            foreach ($groups as $groupId => $groupData) {
                $groupPath = $subject->getSection() . '/' . $groupId;
                if ($groupPath === 'wyomind_elasticsearchcore/configuration' || $groupPath === 'wyomind_elasticsearchautocomplete/settings' || $groupPath === 'wyomind_elasticsearchmultifacetedautocomplete/settings' || $groupPath === 'wyomind_elasticsearchlayerednavigation/settings') {

                    if ($subject->getStore() == 0) {
                        foreach ($this->storeManager->getStores() as $store) {
                            $this->configHelper->updateCanUseModulesInLayout([], $store->getId());
                        }
                    } else {
                        $this->configHelper->updateCanUseModulesInLayout([], $subject->getStore());
                    }
                }
            }
        }
    }
}