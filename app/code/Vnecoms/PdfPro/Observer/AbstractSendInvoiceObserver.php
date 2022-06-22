<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendInvoiceObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendInvoiceObserver extends AbstractObserver
{
    const XML_PATH_ATTACH_PDF = 'sales_email/invoice/attachpdf';

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Vnecoms\PdfPro\Model\Api\PdfRendererInterface
     */
    protected $pdfRenderer;
    /**
     * @var \Vnecoms\PdfPro\Model\Order\Invoice
     */
    protected $_invoice;

    /**
     * AbstractSendInvoiceObserver constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                    $helper
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Model\Order\Invoice            $invoice
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Model\Order\Invoice $invoice
    ) {
        $this->_helper = $helper;
        $this->pdfRenderer = $pdfRenderer;
        $this->_invoice = $invoice;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enable = $this->_helper->getConfig('pdfpro/general/enabled');

        if ($enable == 0) {
            return;
        }

        //check loaded lib
        if (!$this->isLoadedLib()) {
            return;
        }

        /*
         * @var \Magento\Sales\Api\Data\InvoiceInterface
         */
        $invoice = $observer->getInvoice();

        $config = $this->_helper->getConfig('pdfpro/general/invoice_email_attach');

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }
        $invoiceData = $this->_invoice->initInvoiceData($invoice);

        $this->attachPdf(
            'invoice',
            $this->pdfRenderer->getPdfContent('invoice', array($invoiceData)),
            $this->pdfRenderer->getFileName('invoice', $invoice),
            $observer->getAttachmentContainer()
        );
    }
}
