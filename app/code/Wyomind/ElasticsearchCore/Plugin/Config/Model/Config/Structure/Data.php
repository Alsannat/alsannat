<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Plugin\Config\Model\Config\Structure;

class Data
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory = null;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|null
     */
    public $messageManager = null;

    /**
     * @var null|\Wyomind\ElasticsearchCore\Helper\Data
     */
    public $dataHelper = null;

    /**
     * @var \Magento\Framework\App\Request\Http|null
     */
    public $request = null;

    /**
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Wyomind\ElasticsearchCore\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->_indexerHelperFactory = $indexerHelperFactory;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
        $this->request = $request;
    }

    /**
     * Add dynamic types settings group
     *
     * @param \Magento\Config\Model\Config\Structure\Data $subject
     * @param array $config
     * @return array
     */
    public function beforeMerge(\Magento\Config\Model\Config\Structure\Data $subject, array $config)
    {
        if (stripos($this->request->getUriString(), "section/wyomind_elasticsearch") !== false) {
            $message = $this->dataHelper->getNotificationMessage();
            if ($message != "") {
                $this->messageManager->addNotice($message);
            }
        }
        if (isset($config['config']['system'])) {
            if (isset($config['config']['system']['sections']['wyomind_elasticsearchcore'])) {
                $config['config']['system']['sections']['wyomind_elasticsearchcore']['children']['types'] = $this->_indexerHelperFactory->create()->getDynamicTypes();
            }
        }

        return [$config];
    }
}