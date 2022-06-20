<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Plugin\Framework\Api;

use Amasty\CashOnDelivery\Plugin\Cart\CartTotalRepositoryPlugin;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Registry;
use Magento\Quote\Api\Data\TotalsInterface;

class DataObjectHelperPlugin
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * resolve fatal
     *
     * @see CartTotalRepositoryPlugin::beforeGet
     *
     * @param DataObjectHelper $subject
     * @param object $dataObject
     * @param array $data
     * @param string $interfaceName
     *
     * @return array
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforePopulateWithArray(
        DataObjectHelper $subject,
        $dataObject,
        array $data,
        $interfaceName
    ) {
        if (is_a($interfaceName, TotalsInterface::class, true)
            && $this->registry->registry(CartTotalRepositoryPlugin::REGISTRY_IGNORE_EXTENSION_ATTRIBUTES_KEY)
        ) {
            unset($data[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
        }

        return [$dataObject, $data, $interfaceName];
    }
}
