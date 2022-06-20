<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Model\Formatter;

class Image
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $_configHelper = null;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper = null;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepository = null;

    /**
     * @var \Magento\Framework\View\Design\Theme\FlyweightFactory
     */
    protected $_themeFactory = null;

    /**
     * @param \Wyomind\ElasticsearchCore\Helper\Config $configHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\Config $configHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\Asset\Repository $assetRepository,
        \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory
    )
    {
        $this->_configHelper = $configHelper;
        $this->_imageHelper = $imageHelper;
        $this->_assetRepository = $assetRepository;
        $this->_themeFactory = $themeFactory;
    }

    /**
     * @param mixed $value
     * @param mixed $store
     * @return mixed
     */
    public function format($value, $store = null)
    {
        if ($value == 'no_selection') {
            $value = null;
        } else {
            try {
                $imageSize = $this->_configHelper->getStoreConfig('wyomind_elasticsearchcore/types/product/image_size', $store);
                $image = $this->_imageHelper->init(null, 'thumbnail', [
                    'type' => 'thumbnail',
                    'width' => $imageSize,
                    'height' => $imageSize
                ]);
                $image->setImageFile($value);
                $value = $image->getUrl();
                if ($store->isFrontUrlSecure()) {
                    $value = str_replace('http://', 'https://', $value);
                }
                if (!$this->_configHelper->getEnablePubFolder()) {
                    $value = str_replace('pub/', '', $value);
                }
            } catch (\Exception $e) {
                $themeId = $this->_configHelper->getTheme($store);
                $value = $this->_assetRepository->createAsset(
                    'Magento_Catalog::images/product/placeholder/image.jpg', 
                    ['area' => 'frontend','theme' => $this->_themeFactory->create($themeId)->getThemePath()]
                )->getUrl();
            }
        }

        return $value;
    }
}