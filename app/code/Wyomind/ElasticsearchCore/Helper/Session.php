<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class Session
{
    /**
     * @var \Magento\Framework\Session\SessionManagerInterface|null
     */
    private $_session = null;

    /**
     * @var \Magento\Framework\App\Request\Http|null
     */
    private $_request = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Indexer|null
     */
    private $_indexerHelper = null;
    
    /**
     * Session constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param Indexer $indexerHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SessionManagerInterface $session,
        Indexer $indexerHelper
    )
    {
        $this->_session = $session;
        $this->_request = $request;
        $this->_indexerHelper = $indexerHelper;
    }

    /**
     * @return array
     */
    public function getBrowseData()
    {
        $indice = $this->_request->getParam('indice');
        $type = $this->_request->getParam('type');
        $store = $this->_request->getParam('store');
        $storeId = $this->_request->getParam('storeId');

        if ($indice == null) {
            // use cache
            $cache = $this->_session->getElasticsearchcoreBrowseCache();
            if ($cache != null) {
                list($type, $indice, $store, $storeId) = $cache;
            } else {
                // use first indice found
                $indiceInfo = $this->_indexerHelper->getFirstIndice();
                $indice = $indiceInfo['indice'];
                $type = $indiceInfo['type'];
                $store = $indiceInfo['storeCode'];
                $storeId = $indiceInfo['storeId'];
            }
        }

        return [$type, $indice, $store, $storeId];
    }

    /**
     * @param $data
     */
    public function setBrowseData($data)
    {
        $this->_session->setElasticsearchcoreBrowseCache($data);
    }

    /**
     * Store the list of ids to reindex
     * e.g: before the category update > store the current product related to the category
     * @param string $type
     * @param array $ids
     */
    public function setIdsToReindex($type, $ids)
    {
        $this->_session->setElasticsearchcoreIdsToReindex([$type => $ids]);
    }

    /**
     * Get the list of ids to reindex
     * e.g: after the category update > reindex the product list
     * @param string $type
     * @return array
     */
    public function getIdsToReindex($type)
    {
        $typeList = [];
        $idsToReindex = $this->_session->getElasticsearchcoreIdsToReindex();

        if (array_key_exists($type, $idsToReindex)) {
            $typeList = $idsToReindex[$type];
        }

        return $typeList;
    }
}