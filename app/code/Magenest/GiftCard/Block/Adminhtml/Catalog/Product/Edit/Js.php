<?php
namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit;

class Js extends \Magento\Backend\Block\Template
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    public function getProductType()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        return $product->getTypeId();
    }
}
