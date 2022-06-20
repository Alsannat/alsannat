<?php

/**
 * Block of links in Order view page.
 */

namespace Vnecoms\PdfPro\Block\Sales\Order\Info;

use Magento\Customer\Model\Context;

class Buttons extends \Magento\Sales\Block\Order\Info\Buttons
{
    protected $helper;

    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $httpContext, $data);
    }

    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Sales\Block\Order\Info\Buttons'));

        return parent::_toHtml();
    }

    /**
     * Get url for printing order.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     */
    public function getPrintUrl($order)
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return parent::getPrintUrl($order);
        }
        if (!$this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('pdfpro/guest/print', ['order_id' => $order->getId()]);
        }

        return $this->getUrl('pdfpro/order/print', ['order_id' => $order->getId()]);
    }
}
