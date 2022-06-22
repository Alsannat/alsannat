<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Helper;

class Category extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Cache for category collections
     * @var \Magento\Catalog\Model\ResourceModel\Category\Collection[]
     */
    protected $_categoriesWithPathNames = [];
    
    /**
     * Object manager
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager = null;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context, 
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function createCategoryCollection()
    {
        return $this->_objectManager->create(\Magento\Catalog\Model\ResourceModel\Category\Collection::class);
    }
    
    /**
     * Return given category path name using specified separator
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param string $separator
     * @return string
     */
    public function getCategoryPathName(\Magento\Catalog\Model\Category $category, $separator = ' > ')
    {
        $categoryId = $category->getId();
        $storeId = $category->getStoreId();
        $categoryWithPathNames = $this->getCategoriesWithPathNames($storeId)->getItemById($categoryId);

        if ($categoryWithPathNames) {
            return implode($separator, (array) $categoryWithPathNames->getData('path_names'));
        }

        return $category->getName();
    }
    
    /**
     * Retrieve all categories of given store with path names
     *
     * @param mixed $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategoriesWithPathNames($storeId)
    {
        if (!isset($this->_categoriesWithPathNames[$storeId])) {
            $collection = $this->createCategoryCollection()
                                ->addAttributeToSelect('name')
                                ->setStoreId($storeId);

            foreach ($collection as $category) {
                /** @var \Magento\Catalog\Model\Category $category */
                $category->setData('path_names', new \ArrayObject());
                $pathIds = array_slice($category->getPathIds(), 2);

                if (!empty($pathIds)) {
                    foreach ($pathIds as $pathId) {
                        /** @var \Magento\Catalog\Model\Category $item */
                        $item = $collection->getItemById($pathId);
                        if ($item) {
                            $category->getData('path_names')->append($item->getName());
                        }
                    }
                }
            }

            $this->_categoriesWithPathNames[$storeId] = $collection;
        }

        return $this->_categoriesWithPathNames[$storeId];
    }
}