<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\ResourceModel\Index;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Wyomind\ElasticsearchCore\Model\Index', 'Wyomind\ElasticsearchCore\Model\ResourceModel\Index');
    }
}