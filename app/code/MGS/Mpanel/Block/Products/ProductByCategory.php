<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MGS\Mpanel\Block\Products;

/**
 * Main contact form block
 */
class ProductByCategory extends \MGS\Mpanel\Block\Products\AbstractProduct
{
	/**
     * Product collection initialize process
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|Object|\Magento\Framework\Data\Collection
     */
    public function getProductCollection($category)
    {
        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
		
		$now = $this->_date->gmtDate();
		$todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		
        $collection = $this->_productCollectionFactory->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		
		//$collection->addCategoryFilter($category);
		if($category->getId()){
			$categoryIdArray = [$category->getId()];
			$categoryFilter = ['eq'=>$categoryIdArray];
			$collection->addCategoriesFilter($categoryFilter);
		}

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
			->addAttributeToFilter(
				'news_from_date',
				[
					'or' => [
						0 => ['date' => true, 'to' => $todayEndOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				'news_to_date',
				[
					'or' => [
						0 => ['date' => true, 'from' => $todayStartOfDayDate],
						1 => ['is' => new \Zend_Db_Expr('not null')],
					]
				],
				'left'
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
					['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_from_date', array('lt' => $now)],
				]
			)->addAttributeToFilter(
				[
					['attribute' => 'news_to_date', array('gt' => $now)],
				]
			)->addAttributeToSort(
				'news_from_date',
				'desc'
			);

        $collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage())
			->setOrder('entity_id', 'DESC');
			var_dump($collection->getSize());
			die();
        return $collection;
    }
	
	/**
	 * getProductByCategories function
	 *
	 * @param int $categoryIds
	 * @return void
	 */
	public function getProductByCategories($categoryIds){
		
		$now = $this->_date->gmtDate();
		$todayStartOfDayDate = $this->_localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate = $this->_localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');
		
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
		// below line creating issue in getting correct product collection on home page slider
        // $collection->addAttributeToFilter('custom_position',  array('notnull' => true));
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
		$categoryFilter = ['eq'=>$categoryIds];
        $collection->addCategoriesFilter($categoryFilter);

		$collection->setPageSize($this->getLimit())
            ->setCurPage($this->getCurrentPage())
			->setOrder('cat_index_position', 'ASC');
        return $collection;
	}
	
	public function getAllProductCount(){
		//return $this->_count;
	}
	
	/**
	 * getCurrentPage function
	 *
	 * @return void
	 */
	public function getCurrentPage(){
		if ($this->getCurPage()) {
            return $this->getCurPage();
        }
		return 1;
	}
	
	/**
	 * getProductsPerRow function
	 *
	 * @return void
	 */
	public function getProductsPerRow(){
		if ($this->hasData('per_row')) {
            return $this->getData('per_row');
        }
		return false;
	}
	
	/**
	 * getCategoryByIds function
	 *
	 * @return void
	 */
	public function getCategoryByIds(){
		$result = [];
		if($this->hasData('category_ids')){
			$categoryIds = $this->getData('category_ids');
			$categoryArray = explode(',',$categoryIds);
			if(count($categoryArray)>0){
				foreach($categoryArray as $categoryId){
					$category = $this->getModel('Magento\Catalog\Model\Category')->load($categoryId);
					if($category->getId()){
						$result[] = $category;
					}
					
				}
			}
		}
		return $result;
	}
}


