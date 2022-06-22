<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


namespace Amasty\Oaction\Model\Command;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\ResourceModel\GridPool;

class Status extends \Amasty\Oaction\Model\Command
{
    /**
     * @var \Amasty\Oaction\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory
     */
    private $orderStatusFactory;

    /**
     * @var InvoiceService
     */
    private $invoiceService;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender
     */
    private $commentSender;

    /**
     * @var GridPool
     */
    private $gridPool;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    public function __construct(
        \Amasty\Oaction\Helper\Data $helper,
        \Magento\Sales\Api\OrderManagementInterface $orderApi,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Sales\Model\Order\Email\Sender\OrderCommentSender $commentSender,
        Registry $registry,
        InvoiceSender $invoiceCommentSender,
        GridPool $gridPool,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $orderStatusFactory,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct();

        $this->helper = $helper;
        $this->orderApi = $orderApi;
        $this->invoiceService = $invoiceService;
        $this->invoiceCommentSender = $invoiceCommentSender;
        $this->registry = $registry;
        $this->commentSender = $commentSender;
        $this->gridPool = $gridPool;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Executes the command
     *
     * @param AbstractCollection $collection
     * @param $status
     * @param $oaction
     * @return \Magento\Framework\Phrase|string
     */
    public function execute(AbstractCollection $collection, $status, $oaction)
    {
        $numAffectedOrders = 0;
        $comment = __('Status changed');

        foreach ($collection as $order) {
            /** @var \Magento\Sales\Model\Order $order */
            $orderIncrementId = $order->getIncrementId();
            $order = $this->orderRepository->get($order->getId());

            try {
                if ($this->helper->getModuleConfig('status/check_state')) {
                    $state = $order->getState();
                    $statuses = $this->orderStatusFactory->create()
                        ->addStateFilter($state)
                        ->toOptionHash();

                    if (!array_key_exists($status, $statuses)) {
                        $errorMessage = __('Selected status does not correspond to the state of order.');
                        $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
                        continue;
                    }
                }

                $statusHistory = $order->addStatusHistoryComment($comment, $status);
                $statusHistory->setIsVisibleOnFront(false);
                $statusHistory->setIsCustomerNotified(true);
                $statusHistory->save();

                //compatibility for order status extention
                if ($statusHistory->getIsCustomerNotified() === true) {
                    $this->commentSender->send($order, $statusHistory->getIsCustomerNotified(), $comment);
                }

                $order->save();
                ++$numAffectedOrders;
                $this->gridPool->refreshByOrderId($order->getId());
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                $this->_errors[] = __('Can not update order #%1: %2', $orderIncrementId, $errorMessage);
            }

            unset($order);
        }

        $success = ($numAffectedOrders)
            ? $success = __('Total of %1 order(s) have been successfully updated.', $numAffectedOrders)
            : '';

        return $success;
    }
}
