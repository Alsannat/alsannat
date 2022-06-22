<?php

namespace Wyomind\ElasticsearchCore\Plugin\Catalog\Model\Product;

class Action
{

    /**
     * @var \Magento\Framework\Event\ManagerInterface|null
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Action\Rows
     */
    protected $_productPriceIndexerRows;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Catalog\Model\Indexer\Product\Price\Action\Rows $productPriceIndexerRows
    )
    {
        $this->_eventManager = $eventManager;
        $this->_productPriceIndexerRows = $productPriceIndexerRows;
    }

    /**
     * @param Interceptor $interceptor
     * @param \Closure $closure
     * @param $productIds
     * @param $attrData
     * @param $storeId
     * @return Interceptor
     */
    public function aroundUpdateAttributes(
        \Magento\Catalog\Model\Product\Action\Interceptor $interceptor,
        \Closure $closure,
        $productIds,
        $attrData,
        $storeId
    )
    {
        $result = $closure($productIds, $attrData, $storeId);
        $this->_productPriceIndexerRows->execute($productIds);
        $this->_eventManager->dispatch("catalog_product_attribute_update_after", ['id' => $productIds]);
        return $result;
    }
}