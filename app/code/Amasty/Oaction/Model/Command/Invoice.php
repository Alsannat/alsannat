<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */

declare(strict_types=1);

namespace Amasty\Oaction\Model\Command;

use Amasty\Oaction\Helper\Data;
use Amasty\Oaction\Model\Command;
use Magento\Framework\DB\TransactionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Phrase;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice as OrderInvoice;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\Order\Pdf\Invoice as PdfInvoice;
use Magento\Sales\Model\Service\InvoiceService;

class Invoice extends Command
{
    protected $objectManager;

    /**
     * @var Data
     */
    private $oActionHelper;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var PdfInvoice
     */
    private $pdfInvoice;

    /**
     * @var InvoiceFactory
     */
    private $invoiceFactory;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    private $pdf = null;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Data $oActionHelper,
        InvoiceService $invoiceService,
        InvoiceSender $invoiceCommentSender,
        OrderRepositoryInterface $orderRepository,
        PdfInvoice $pdfInvoice,
        InvoiceFactory $invoiceFactory,
        TransactionFactory $transactionFactory
    ) {
        parent::__construct();
        $this->objectManager = $objectManager;
        $this->oActionHelper = $oActionHelper;
        $this->invoiceService = $invoiceService;
        $this->invoiceSender = $invoiceCommentSender;
        $this->orderRepository = $orderRepository;
        $this->pdfInvoice = $pdfInvoice;
        $this->invoiceFactory = $invoiceFactory;
        $this->transactionFactory = $transactionFactory;
    }

    public function execute(AbstractCollection $collection, int $notifyCustomer, array $oaction): string
    {
        $numAffectedOrders = 0;
        $comment = __('Invoice created');
        $capture = (int) $this->oActionHelper->getModuleConfig('invoice/capture');
        $orderStatus = $this->oActionHelper->getModuleConfig('invoice/status');
        $print = $this->oActionHelper->getModuleConfig('invoice/print');

        foreach ($collection as $order) {
            try {
                $this->checkOrder($order);
                $invoice = $this->createInvoice($order, $capture, $notifyCustomer, $comment);

                if ($orderStatus) {
                    $order->addStatusToHistory($orderStatus, '', $notifyCustomer);
                    $this->orderRepository->save($order);
                }

                $invoiceIncrementId = $invoice->getIncrementId();
                // send invoice emails
                if ($notifyCustomer) {
                    try {
                        $this->invoiceSender->send($invoice);
                    } catch (\Exception $e) {
                        $errorMessage = $e->getMessage();
                        $this->_errors[] = __(
                            'Can not send the invoice email for invoice %1: %2',
                            $invoiceIncrementId,
                            $errorMessage
                        );
                    }
                }

                if ($invoiceIncrementId && $print) {
                    if (!$invoice) {
                        $invoice = $this->invoiceFactory->create()->loadByIncrementId($invoiceIncrementId);
                    }

                    $this->collectPdf($invoice);
                }

                ++$numAffectedOrders;
            } catch (LocalizedException $e) {
                $orderIncrementId = $order->getIncrementId();
                $errorMessage = $e->getMessage();
                $this->_errors[] = __('Can not invoice order #%1: %2', $orderIncrementId, $errorMessage);
            }

            $order = null;
            unset($order);
        }

        return __('Total of %1 order(s) has been successfully invoiced', $numAffectedOrders)->render();
    }

    public function hasResponse(): bool
    {
        return !empty($this->pdf);
    }

    public function getResponseName(): string
    {
        return 'invoices_' . $this->oActionHelper->getDate() . '.pdf';
    }

    public function getResponseBody(): string
    {
        return $this->pdf->render();
    }

    private function checkOrder(Order $order)
    {
        if (!$order->getId()) {
            throw new LocalizedException(__('The order no longer exists.'));
        }

        if (!$order->canInvoice()) {
            throw new LocalizedException(__('The order does not allow an invoice to be created.'));
        }
    }

    private function createInvoice(Order $order, int $capture, int $notifyCustomer, Phrase $comment): OrderInvoice
    {
        $invoice = $this->invoiceService->prepareInvoice($order);

        if (!$invoice) {
            throw new LocalizedException(__('Can not save the invoice right now.'));
        }

        if (!$invoice->getTotalQty()) {
            throw new LocalizedException(__('You can not create an invoice without products.'));
        }

        $invoice->addComment($comment, $notifyCustomer);
        $invoice->setCustomerNote($comment);

        switch ($capture) {
            case OrderInvoice::CAPTURE_ONLINE:
                $invoice->setRequestedCaptureCase(OrderInvoice::CAPTURE_ONLINE);
                break;
            case OrderInvoice::CAPTURE_OFFLINE:
                $invoice->setRequestedCaptureCase(OrderInvoice::CAPTURE_OFFLINE);
                break;
        }

        $invoice->register();
        $invoice->getOrder()->setCustomerNoteNotify($notifyCustomer);
        $invoice->getOrder()->setIsInProcess(true);

        $saveTransaction = $this->transactionFactory->create();
        $saveTransaction->addObject($invoice)->addObject($invoice->getOrder());
        $saveTransaction->save();

        return $invoice;
    }

    private function collectPdf(OrderInvoice $invoice)
    {
        if (!isset($this->pdf)) {
            $this->pdf = $this->pdfInvoice->getPdf([$invoice]);
        } else {
            $newPdf = $this->pdfInvoice->getPdf([$invoice]);
            $this->pdf->pages = array_merge($this->pdf->pages, $newPdf->pages);
        }
    }
}
