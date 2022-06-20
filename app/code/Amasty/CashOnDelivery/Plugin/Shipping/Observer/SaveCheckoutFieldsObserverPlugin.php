<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Plugin\Shipping\Observer;

use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Event\Observer;
use Temando\Shipping\Observer\SaveCheckoutFieldsObserver;

class SaveCheckoutFieldsObserverPlugin
{
    /**
     * @var AttributeValue
     */
    private $attributeValue;

    public function __construct(AttributeValue $attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }

    /**
     * @param SaveCheckoutFieldsObserver $subject
     * @param Observer $observer
     *
     * @return array
     *
     * @codingStandardsIgnoreStart
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(SaveCheckoutFieldsObserver $subject, Observer $observer)
    {
        /** @var \Magento\Quote\Api\Data\AddressInterface|\Magento\Quote\Model\Quote\Address $quoteAddress */
        $quoteAddress = $observer->getData('quote_address');

        if ($quoteAddress->getAddressType() !== \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
            return [$observer];
        }

        if (!$quoteAddress->getExtensionAttributes()) {
            return [$observer];
        }

        $extensionAttributes = $quoteAddress->getExtensionAttributes();

        if (method_exists($extensionAttributes, 'getCheckoutFields')
            && !$extensionAttributes->getCheckoutFields()
        ) {
            $checkoutFields = [$this->attributeValue];
            $extensionAttributes->setCheckoutFields($checkoutFields);
        }

        return [$observer];
    }
}
