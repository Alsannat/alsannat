<?php 
namespace Evincemage\ProductAttributes\Helper;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Pricing\Price\ConfiguredPriceInterface;

class PopupHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Block\Product\ImageBuilder $imageBuilder,
		ItemResolverInterface $itemResolver = null,
		\Magento\Catalog\Helper\Product\Configuration $productConfig
	)
	{
		$this->storeManager = $storeManager;
		$this->imageBuilder = $imageBuilder;
		$this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
		$this->_productConfig = $productConfig;
		parent::__construct($context);
	}

	public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
 
    public function getMediaUrl()
    {
        return $this->getBaseUrl() . 'media/';
    }
 
    public function getProductImageUrl($fileName)
    {
        return $this->getMediaUrl() . 'catalog/product/em_images/' . $fileName;
    }

    public function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->create($product, $imageId, $attributes);
    }

    public function getProductForThumbnail($item)
    {
        return $this->itemResolver->getFinalProduct($item);
    }

    public function getQty($item)
    {
        if ((string)$item->getQty()=='') 
        {
            return 1;
        }

        return $item->getQty() * 1;
    }

    public function getProductOptions($item)
    {
        $optionsArray = [];
        $result = [];
        $options = $item->getProductOptions();
        if(is_null($options))
        {
            $optionsArray = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
            if(isset($optionsArray['attributes_info']))
            {
                $result = $optionsArray['attributes_info'];
                return $result;
            }
        }

        return $result;
    }

    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->_productConfig;
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];
        return $helper->getFormattedOptionValue($optionValue, $params);
    }

    public function getUnitPriceHtml(AbstractItem $item, $blockObj)
    {
        /** @var Renderer $block */
        $block = $blockObj->getLayout()->getBlock('checkout.item.price.unit');
        $block->setItem($item);
        return $block->toHtml();
    }


}