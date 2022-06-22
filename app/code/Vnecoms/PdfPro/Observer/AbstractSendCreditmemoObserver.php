<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendCreditmemoObserver.
 */
class AbstractSendCreditmemoObserver extends AbstractObserver
{
    const XML_PATH_ATTACH_PDF = 'pdfpro/general/order_email_attach';

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Vnecoms\PdfPro\Model\Api\PdfRendererInterface
     */
    protected $pdfRenderer;
    /**
     * @var \Vnecoms\PdfPro\Model\Order\Creditmemo
     */
    protected $_creditmemo;

    /**
     * AbstractSendCreditmemoObserver constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                    $helper
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Model\Order\Creditmemo         $creditmemo
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Model\Order\Creditmemo $creditmemo
    ) {
        $this->_helper = $helper;
        $this->pdfRenderer = $pdfRenderer;
        $this->_creditmemo = $creditmemo;
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
         * @var \Magento\Sales\Api\Data\CreditmemoInterface
         */
        $creditmemo = $observer->getCreditmemo();
        $config = $this->_helper->getConfig('pdfpro/general/creditmemo_email_attach');

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }

        $creditmemoData = $this->_creditmemo->initCreditmemoData($creditmemo);

        $this->attachPdf(
            'creditmemo',
            $this->pdfRenderer->getPdfContent('creditmemo', array($creditmemoData)),
            $this->pdfRenderer->getFileName('creditmemo', $creditmemo),
            $observer->getAttachmentContainer()
        );
    }
}
