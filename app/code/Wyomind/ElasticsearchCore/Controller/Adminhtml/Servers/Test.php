<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Controller\Adminhtml\Servers;

/**
 * Class Test
 */
class Test extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_ElasticsearchCore::main';

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    )
    {
        parent::__construct($context);
        $this->_jsonHelper = $jsonHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $warnings = [];
        $hosts = explode(',', $this->getRequest()->getParam('servers'));
        foreach ($hosts as $host) {
            $client = \Wyomind\ElasticsearchCore\Elasticsearch\ClientBuilder::create()->setHosts([$host])->build();
            try {
                $warnings[] = ['host' => $host, 'data' => $client->info(['client' => ['verify' => false, 'connect_timeout' => 5]])];
            } catch (\Exception $e) {
                $warnings[] = ['host' => $host, 'error' => $e->getMessage()];
            }
        }

        return $this->getResponse()->representJson($this->_jsonHelper->jsonEncode($warnings));
    }
}