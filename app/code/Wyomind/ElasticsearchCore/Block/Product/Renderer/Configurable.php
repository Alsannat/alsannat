<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Block\Product\Renderer;

/**
 * Class Configurable for Magento 2.2
 */
class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{
    /**
     * @var \Magento\Catalog\Helper\Image|null
     */
    protected $_imageHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config|null
     */
    protected $_config = null;

    /**
     * Configurable constructor
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Wyomind\ElasticsearchCore\Helper\Config $config
     * @param array $data
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\ConfigurableProduct\Model\ConfigurableAttributeData $configurableAttributeData,
        \Wyomind\ElasticsearchCore\Helper\Config $config,
        array $data = [],
        \Magento\Framework\Locale\Format $localeFormat = null,
        \Magento\Customer\Model\Session $customerSession = null
    )
    {
        parent::__construct(
            $context, $arrayUtils, $jsonEncoder, $helper, $catalogProduct, $currentCustomer, $priceCurrency, $configurableAttributeData, $data, $localeFormat, $customerSession
        );
        $this->_config = $config;
        $this->_imageHelper = $context->getImageHelper();
    }

    /**
     * @return array
     */
    protected function getOptionImages()
    {
        $images = [];
        foreach ($this->getAllowProducts() as $product) {
            $productImages = $this->helper->getGalleryImages($product) ?: [];
            foreach ($productImages as $image) {
                if ($image->getFile() == $product->getImage()) {
                    $imageObj = $this->_imageHelper->init(null, 'base', [
                        'type' => 'base',
                        'width' => 240,
                        'height' => 300
                    ]);
                    $imageObj->setImageFile($image->getFile());
                    $value = $imageObj->getUrl();
                    if ($this->getCurrentStore()->isFrontUrlSecure()) {
                        $value = str_replace("http://", "https://", $value);
                    }
                    if (!$this->_config->getEnablePubFolder()) {
                        $value = str_replace("pub/", "", $value);
                    }

                    $images[$product->getId()] = [
                        'img' => $value,
                        'caption' => $image->getLabel(),
                    ];
                }
            }
        }

        return $images;
    }
}