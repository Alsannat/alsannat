<?php

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 29/06/2017
 * Time: 21:49
 */
namespace Magenest\GiftCard\Block\Adminhtml\Items;

class Renderer extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry,$optionFactory, $data);
    }

    public function getGiftCardAmount() {
        $item = $this->getItem();

        $giftcardAmount = $item->getData('giftcard_amount');
        return $giftcardAmount;
    }

    public function toHtml()
    {
        $amount = $this->getGiftCardAmount();
        if ($amount) {
            return $amount;
        } else {
            return '';
        }
    }
}