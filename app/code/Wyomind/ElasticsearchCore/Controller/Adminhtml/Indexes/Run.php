<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Controller\Adminhtml\Indexes;

/**
 * Class Run
 */
class Run extends \Magento\Backend\App\Action
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
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    protected $_indexerHelper = null;

    /**
     * Run constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
    )
    {
        $this->_resultRedirectFactory = $context->getResultRedirectFactory();
        $this->_indexerHelper = $indexerHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $indexerId = $this->getRequest()->getParam('indexer_id');
        $indexer = $this->_indexerHelper->getIndexer($indexerId);
        try {
            $indexer->reindex($indexerId);
            $this->messageManager->addSuccess(__('The indexer ' . $indexerId . ' has been reindexed'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $result = $this->_resultRedirectFactory->create()->setPath(\Wyomind\ElasticsearchCore\Helper\Url::MANAGE_INDEXES);

        return $result;
    }
}