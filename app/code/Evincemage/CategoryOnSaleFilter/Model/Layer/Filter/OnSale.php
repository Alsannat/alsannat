<?php

namespace Evincemage\CategoryOnSaleFilter\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use MGS\Ajaxlayernavigation\Model\Layer\Filter\DefaultFilter;

class OnSale extends DefaultFilter
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    public $appliyedFilter;
    public $filterPlus;
    public $active;
    public $value;


    
     /*public function __construct(
         \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
         \Magento\Store\Model\StoreManagerInterface $storeManager,
         \Magento\Catalog\Model\Layer $layer,
         \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
         \Magento\Framework\App\ResourceConnection $resourceConnection,
         array $data = []
     ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->resourceConnection = $resourceConnection;
        $this->_requestVar = 'on_sale';
    }*/

    public function __construct(
        \MGS\Ajaxlayernavigation\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \MGS\Ajaxlayernavigation\Model\Layer\Filter\ItemBuilder $itemBuilder,
         array $data = [],
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    )
    {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemBuilder,
            $data
        );

        $this->escaper = $escaper;
        $this->_catalogSession = $catalogSession;
        $this->resourceConnection = $resourceConnection;
        $this->_requestVar = 'on_sale';
        $this->appliedFilter = [];
        $this->filterPlus = false;
        $this->active = false;
        $this->value = true;
    }

    /**
     * Get filter name
     * @return mixed
     */
    public function getName()
    {
        return __('Discounted Only');
    }

    /**
     * {@inheritdoc}
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter || is_array($filter) ) 
        {
            $this->unsetActive();
            $this->unsetApplicationValue();
            return $this;
        }
        
        if($filter=="1")
        {
            //echo "filter is valid";
            $this->setActive(true);
            $this->setApplicationValue(false);
        }
        else
        {
            
            $this->setActive(false);
            $this->setApplicationValue(true);
        }
        
        
        $this->appliedFilter = $filter;
        $filters = explode(',', $filter);

        if (!$this->filterPlus) 
        {
            $this->filterPlus = true;
        }
            
            $this->getLayer()
                ->getProductCollection()
                ->getSelect()
                ->where('price_index.final_price > 0 && price_index.final_price < price_index.price');

            $this->getLayer()->getState()->addFilter($this->_createItem('Discounted Only', 1));
        

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getItemsData()
    {
        $onsaleCount = $this->getOnSalesCount($this->getLayer()->getProductCollection());
       if($onsaleCount!=="0")
       {
            $this->_itemBuilder->addItemData(
                __('Discounted Only'),
                $this->getApplicationValue(),
                $onsaleCount,
                $this->getActive(),
                $this->filterPlus
            ); 
       } 
       

        $this->unsetActive();
        $this->unsetApplicationValue();
        return $this->_itemBuilder->build();
    }

    /**
     * Get count of how many products are in sale on the currenct filter
     * @param  \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return integer
     */
    private function getOnSalesCount(\Magento\Catalog\Model\ResourceModel\Product\Collection $collection)
    {
        $select = clone $collection->getSelect();
        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $select->columns('count(*) as count');
        $select->where('price_index.final_price > 0 && price_index.final_price < price_index.price');

        $connection = $this->resourceConnection->getConnection();
        $result = $connection->fetchAll($select);
        $count = (int)$result[0]['count'] > 0 ? $result[0]['count'] : "0";
        return $count;
    }

    public function isActive()
    {
        return $this->filterPlus;
    }

    public function setActive($value)
    {
        if(!is_null($value))
        {
            $this->getCatalogSession()->setDiscountIsAcive($value);
        }
    }

    public function getActive()
    {
        return $this->getCatalogSession()->getDiscountIsAcive() === null ? false : $this->getCatalogSession()->getDiscountIsAcive();
    }

    public function setApplicationValue($value)
    {
        if(!is_null($value))
        {
            $this->getCatalogSession()->setDiscountFilterValue($value);
        }
    }

    public function getApplicationValue()
    {
        return $this->getCatalogSession()->getDiscountFilterValue()===null? true : $this->getCatalogSession()->getDiscountFilterValue();
    }

    public function getCatalogSession() 
    {
        return $this->_catalogSession;
    }

    public function unsetActive()
    {
        return $this->getCatalogSession()->unsDiscountIsAcive();
    }

    public function unsetApplicationValue()
    {
        $this->getCatalogSession()->unsDiscountFilterValue();
    }

}
