<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Block\Checkout\Cart;

use Amasty\CashOnDelivery\Model\ConfigProvider;
use Magento\Framework\View\Element\Template;

class CheckAvailable extends Template
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        Template\Context $context,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configProvider = $configProvider;
    }

    /**
     * @return bool
     */
    public function isVerificationEnable()
    {
        return $this->configProvider->isCodeVerificationEnabled();
    }
}
