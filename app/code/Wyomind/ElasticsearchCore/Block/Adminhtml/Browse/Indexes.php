<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\Browse;

/**
 * Class Indexes
 */
class Indexes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    private $_indexerHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Session|null
     */
    private $_sessionHelper = null;

    /**
     * Indexes constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Wyomind\ElasticsearchCore\Helper\Indexer $indexerHelper,
        \Wyomind\ElasticsearchCore\Helper\Session $sessionHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_indexerHelper = $indexerHelper;
        $this->_sessionHelper = $sessionHelper;
    }

    /**
     * Get all indexers
     * @return array
     */
    public function getIndexers()
    {
        return $this->_indexerHelper->getAllIndexers();
    }

    /**
     * Get all indices for a specific type
     * @param $type
     * @return array
     */
    public function getIndices($type)
    {
        return $this->_indexerHelper->getIndices($type);
    }

    /**
     * Get the current indice (from the $request object)
     * @return mixed
     */
    public function getSelectedIndice()
    {
        list($type, $indice, $store, $storeId) = $this->_sessionHelper->getBrowseData();
        return $indice;
    }
}