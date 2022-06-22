<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Controller\Adminhtml\Indexes;

/**
 * @package Wyomind\ElasticsearchCore\Controller\Adminhtml\Indexes
 */
class Flush extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_ElasticsearchCore::indexer';

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory|null
     */
    protected $_resultRedirectFactory = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    protected $_indexerHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Model\Adapter
     */
    protected $_adapter = null;

    /**
     * Flush constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     * @param \Wyomind\ElasticsearchCore\Model\Adapter $adapter
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper,
        \Wyomind\ElasticsearchCore\Model\Adapter $adapter
    )
    {
        $this->_resultRedirectFactory = $context->getResultRedirectFactory();
        $this->_storeManager = $storeManager;
        $this->_indexerHelper = $indexerHelper;
        $this->_adapter = $adapter;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $indexerId = $this->getRequest()->getParam('indexer_id');

        foreach ($this->_storeManager->getStores() as $store) {
            $this->_adapter->deleteDocs($indexerId, $store->getStoreId(), []);
        }

        $this->messageManager->addSuccess(__('The indexer ' . $indexerId . ' has been flushed'));

        $result = $this->_resultRedirectFactory->create()->setPath(\Wyomind\ElasticsearchCore\Helper\Url::MANAGE_INDEXES);
        return $result;
    }
}