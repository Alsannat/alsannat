<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Block\Adminhtml\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Module\Manager;

class ShippingComment extends Field
{
    /**
     * @var string
     */
    const PAYMENT_COMMENT = "Select all or clear the selection to allow all shipping methods. If you want to apply more"
    ." restrictions, please use <a href='%1' target='_blank'>Payment Restrictions</a> extension.";

    /**
     * @var string
     */
    const AMASTY_PAYMENT_RESTRICTION_URL = 'https://amasty.com/payment-restrictions-for-magento-2.html?utm_source'
    . '=extension&utm_medium=backend&utm_campaign=cash-on-delivery-allowed-shipping-methods';

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(
        Context $context,
        Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
    }


    /**
     * @inheritdoc
     */
    public function render(AbstractElement $element)
    {
        if ($this->moduleManager->isEnabled('Amasty_Payrestriction')) {
            $url = $this->getUrl('amasty_payrestriction/rule/newAction');
            $element->setComment(__(self::PAYMENT_COMMENT, $url));
        } else {
            $element->setComment(__(self::PAYMENT_COMMENT, self::AMASTY_PAYMENT_RESTRICTION_URL));
        }

        return parent::render($element);
    }
}
