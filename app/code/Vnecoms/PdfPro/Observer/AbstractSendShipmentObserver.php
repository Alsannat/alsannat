<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendShipmentObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendShipmentObserver extends AbstractObserver
{
    const XML_PATH_ATTACH_PDF = 'sales_email/shipment/attachpdf';
    const XML_PATH_ATTACH_AGREEMENT = 'sales_email/shipment/attachagreement';

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Vnecoms\PdfPro\Model\Api\PdfRendererInterface
     */
    protected $pdfRenderer;
    /**
     * @var \Vnecoms\PdfPro\Model\Order\Shipment
     */
    protected $_shipment;

    /**
     * AbstractSendShipmentObserver constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                    $helper
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Model\Order\Shipment           $shipment
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Model\Order\Shipment $shipment
    ) {
        $this->_helper = $helper;
        $this->pdfRenderer = $pdfRenderer;
        $this->_shipment = $shipment;
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
         * @var \Magento\Sales\Api\Data\ShipmentInterface
         */
        $shipment = $observer->getShipment();
        $config = $this->_helper->getConfig('pdfpro/general/shipment_email_attach');

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }

        $shipmentData = $this->_shipment->initShipmentData($shipment);

        $this->attachPdf(
            'shipment',
            $this->pdfRenderer->getPdfContent('shipment', array($shipmentData)),
            $this->pdfRenderer->getFileName('shipment', $shipment),
            $observer->getAttachmentContainer()
        );
    }
}
