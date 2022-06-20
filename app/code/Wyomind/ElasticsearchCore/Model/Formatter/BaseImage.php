<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Model\Formatter;

class BaseImage
{
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Framework\View\Design\Theme\FlyweightFactory
     */
    protected $themeFactory;

    /**
     * BaseImage constructor.
     * @param \Wyomind\ElasticsearchCore\Helper\Config $config
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory
     */
    public function __construct(
        \Wyomind\ElasticsearchCore\Helper\Config $config,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\Design\Theme\FlyweightFactory $themeFactory
    )
    {
        $this->config = $config;
        $this->imageHelper = $imageHelper;
        $this->assetRepo = $assetRepo;
        $this->themeFactory = $themeFactory;
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
                $image = $this->imageHelper->init(null, 'base', [
                    'type' => 'base',
                    'width' => 240,
                    'height' => 300
                ]);
                $image->setImageFile($value);
                $value = $image->getUrl();
                if ($store->isFrontUrlSecure()) {
                    $value = str_replace("http://", "https://", $value);
                }
                if (!$this->config->getEnablePubFolder()) {
                    $value = str_replace("pub/", "", $value);
                }
            } catch (\Exception $e) {
                $themeId = $this->config->getTheme($store);
                $value = $this->assetRepo
                    ->createAsset('Magento_Catalog::images/product/placeholder/image.jpg', [
                        'area' => 'frontend',
                        'theme' => $this->themeFactory->create($themeId)->getThemePath(),
                    ])
                    ->getUrl();
            }
        }

        return $value;
    }
}