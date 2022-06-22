<?php

namespace Evincemage\AmastyAcartExtended\Block\Emails;

use Amasty\Acart\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory as LinkCollectionFactory;
use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;
use Magento\Catalog\Model\Product\Visibility;

abstract class LinkExtended extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var LinkCollectionFactory
     */
    private $linkCollectionFactory;

    /**
     * @var ProductLinkFactory
     */
    protected $productLinkFactory;

    public function __construct(
        ConfigProvider $configProvider,
        LinkCollectionFactory $linkCollectionFactory,
        ProductLinkFactory $productLinkFactory,
        Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Amasty\Mostviewed\Block\Widget\Related $relatedProductsCollection,
        \Evincemage\AmastyAcartExtended\Helper\Data $itemsHelper,
        array $data = []
    ) {
        $this->configProvider = $configProvider;
        $this->linkCollectionFactory = $linkCollectionFactory;
        $this->productLinkFactory = $productLinkFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->relatedProductsCollection = $relatedProductsCollection;
        $this->itemsHelper = $itemsHelper;
        parent::__construct($context, $data);
    }

    public function getItems()
    {
        
        $skuString = $this->itemsHelper->getProductSkus();
        if(!is_null($skuString)&&!empty($skuString))
        {
            $skuArray = explode(",", $skuString);
        }
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if(count($skuArray)>0)
        {
            $collection->addAttributeToFilter('sku', ['in' => $skuArray]);
        }

        if ($qty = $this->configProvider->getProductsQty()) 
        {
            $collection->setPageSize($qty);
        }

        return $collection;
    }

    abstract protected function getLinkModel();
}
