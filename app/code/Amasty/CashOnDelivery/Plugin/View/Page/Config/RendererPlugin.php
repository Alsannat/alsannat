<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Plugin\View\Page\Config;

use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Page\Config\Renderer;

class RendererPlugin
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Repository
     */
    private $assetRepository;

    /**
     * @var GroupedCollection
     */
    private $pageAssets;

    public function __construct(
        ConfigProvider $configProvider,
        Repository $assetRepository,
        GroupedCollection $pageAssets
    ) {
        $this->configProvider = $configProvider;
        $this->assetRepository = $assetRepository;
        $this->pageAssets = $pageAssets;
    }

    /**
     * @param Renderer $subject
     * @param array $resultGroups
     *
     * @return array
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeRenderAssets(Renderer $subject, $resultGroups = [])
    {
        if (!$this->configProvider->isPaymentFeeEnabled() || !$this->configProvider->isCashOnDeliveryEnabled()) {
            $this->disablePayment();
        }

        return [$resultGroups];
    }

    private function disablePayment()
    {
        $file = 'Amasty_CashOnDelivery::js/amastyCashOnDeliveryDisabled.js';
        $asset = $this->assetRepository->createAsset($file);
        $this->pageAssets->insert($file, $asset, 'requirejs/require.js');
    }
}
