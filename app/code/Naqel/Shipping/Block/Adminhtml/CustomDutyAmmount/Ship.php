<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Block\Adminhtml\CustomDutyAmmount;

class Ship extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        if ($this->getOrder()->getShippingMethod() == 'naqelshipping_naqelshipping') {
            return parent::_toHtml();
        }
    }
}
