<?php

namespace Vnecoms\PdfPro\Block\Sales\Order\Invoice;

class Items extends \Magento\Sales\Block\Order\Invoice\Items
{
    protected $helper;

    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $data);
    }

    protected function _toHtml()
    {
        $this->setModuleName($this->extractModuleName('Magento\Sales\Block\Order\Invoice\Items'));

        return parent::_toHtml();
    }

    /**
     * @param object $invoice
     *
     * @return string
     */
    public function getPrintInvoiceUrl($invoice)
    {
        if (!$this->helper->getConfig('pdfpro/general/enabled') || !$this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return parent::getPrintInvoiceUrl($invoice);
        }

        return $this->getUrl('pdfpro/order/printInvoice', ['invoice_id' => $invoice->getId()]);
    }

    /**
     * @param object $order
     *
     * @return string
     */
    public function getPrintAllInvoicesUrl($order)
    {
        return parent::getPrintAllInvoicesUrl($order);
        if (!$this->helper->getConfig('pdfpro/general/enabled') || $this->helper->getConfig('pdfpro/general/allow_customer_print')) {
            return parent::getPrintAllInvoicesUrl($order);
        }

        return $this->getUrl('pdfpro/order/printInvoice', ['order_id' => $order->getId()]);
    }
}
