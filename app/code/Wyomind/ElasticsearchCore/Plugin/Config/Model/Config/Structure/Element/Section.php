<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Plugin\Config\Model\Config\Structure\Element;

class Section
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory = null;
    
    /**
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
    )
    {
        $this->_indexerHelperFactory = $indexerHelperFactory;
    }
    
    /**
     * Add dynamic types settings group
     *
     * @param \Magento\Config\Model\Config\Structure\Element\Section $subject
     * @param callable $proceed
     * @param array $data
     * @param $scope
     * @return mixed
     */
    public function aroundSetData(\Magento\Config\Model\Config\Structure\Element\Section $subject, callable $proceed, array $data, $scope)
    {
        // This method runs for every section. Add a condition to check for the one to which we are interested in adding groups
        if ($data['id'] == 'wyomind_elasticsearchcore') {
            $data['children']['types'] = $this->_indexerHelperFactory->create()->getDynamicTypes();
        }

        return $proceed($data, $scope);
    }
}