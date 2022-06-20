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

class FeeComment extends Field
{
    /**
     * @var string
     */
    const FEE_COMMENT = "Floating point numbers only. Up to 2 digits after the point. Should be greater than 0. "
    . "To set more flexible fees, use <a href='%1' target='_blank'>Extra Fee</a> extension.";

    /**
     * @var string
     */
    const AMASTY_FEE_URL = 'https://amasty.com/extra-fee-for-magento-2.html?utm_source=extension&utm_medium=backend'
    . '&utm_campaign=cash-on-delivery-fee';

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
        if ($this->moduleManager->isEnabled('Amasty_Extrafee')) {
            $url = $this->getUrl('amasty_extrafee/index/new');
            $element->setComment(__(self::FEE_COMMENT, $url));
        } else {
            $element->setComment(__(self::FEE_COMMENT, self::AMASTY_FEE_URL));
        }

        return parent::render($element);
    }
}
