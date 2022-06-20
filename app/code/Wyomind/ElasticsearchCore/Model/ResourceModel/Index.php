<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\ResourceModel;

class Index extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('wyomind_elasticsearchcore_index', 'id');
    }
}