<?php

namespace Vnecoms\PdfPro\Observer;

/**
 * Class AbstractSendOrderObserver.
 *
 * @author Vnecoms team <vnecoms.com>
 */
class AbstractSendOrderObserver extends AbstractObserver
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
     * @var \Vnecoms\PdfPro\Model\Order
     */
    protected $_order;

    /**
     * AbstractSendOrderObserver constructor.
     *
     * @param \Vnecoms\PdfPro\Helper\Data                    $helper
     * @param \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer
     * @param \Vnecoms\PdfPro\Model\Order                    $order
     */
    public function __construct(
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Vnecoms\PdfPro\Model\Api\PdfRendererInterface $pdfRenderer,
        \Vnecoms\PdfPro\Model\Order $order
    ) {
        $this->_helper = $helper;
        $this->pdfRenderer = $pdfRenderer;
        $this->_order = $order;
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
         * @var \Magento\Sales\Api\Data\OrderInterface
         */
        $order = $observer->getOrder();
        $config = $this->_helper->getConfig('pdfpro/general/order_email_attach');

        if ($config == \Vnecoms\PdfPro\Model\Source\Attach::ATTACH_TYPE_NO) {
            return;
        }
        $orderData = $this->_order->initOrderData($order);

        $this->attachPdf(
            'order',
            $this->pdfRenderer->getPdfContent('order', array($orderData)),
            $this->pdfRenderer->getFileName('order', $order),
            $observer->getAttachmentContainer()
        );
    }
}
