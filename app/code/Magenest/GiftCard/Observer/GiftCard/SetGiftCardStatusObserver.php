<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 09:49
 */

namespace Magenest\GiftCard\Observer\GiftCard;

use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Section\Options\Type\DateTime;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class SetGiftCardStatusObserver implements ObserverInterface
{
    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $_objectCopyService;

    protected $_giftCardFactory;

    protected $_giftCardPurchasedFactory;

    protected $_helper;

    protected $_logger;

    protected $orderItemFactory;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magenest\GiftCard\Model\GiftCardPurchasedFactory $giftCardPurchasedFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\GiftCard\Helper\Data $helper,
         \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\DataObject\Copy $objectCopyService
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_productFactory = $productFactory;
        $this->_giftCardPurchasedFactory = $giftCardPurchasedFactory;
        $this->_objectCopyService = $objectCopyService;
        $this->_helper = $helper;
        $this->_logger = $logger;

        $this->orderItemFactory = $orderItemFactory;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order->getId()) {
            //order not saved in the database
            return $this;
        }

        /* @var $order \Magento\Sales\Model\Order */
        $status = '';
        $giftcardStatuses = [
            'pending' => \Magenest\GiftCard\Model\GiftCardPurchased::STATUS_PENDING,
            'expired' =>  \Magenest\GiftCard\Model\GiftCardPurchased::STATUS_EXPIRED,
            'avail' => \Magenest\GiftCard\Model\GiftCardPurchased::STATUS_AVAILABLE,
            'payment_pending' =>  \Magenest\GiftCard\Model\GiftCardPurchased::STATUS_PAYMENT_REVIEW,
            'payment_review' =>  \Magenest\GiftCard\Model\GiftCardPurchased::STATUS_PAYMENT_REVIEW,
        ];
        $giftcardItemsStatuses = [];
        $orderItemStatusToEnable = $this->_scopeConfig->getValue(
            \Magenest\GiftCard\Model\GiftCardPurchased::XML_PATH_ORDER_ITEM_STATUS,
            ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );

        if (!$orderItemStatusToEnable) {
            $orderItemStatusToEnable =1;
        }

        if ($order->getState() == \Magento\Sales\Model\Order::STATE_HOLDED) {
            $status = $giftcardStatuses['pending'];
        } elseif ($order->isCanceled()
            || $order->getState() == \Magento\Sales\Model\Order::STATE_CLOSED
            || $order->getState() == \Magento\Sales\Model\Order::STATE_COMPLETE
        ) {
            $expiredStatuses = [
                \Magento\Sales\Model\Order\Item::STATUS_CANCELED,
                \Magento\Sales\Model\Order\Item::STATUS_REFUNDED,
            ];
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                    || $item->getRealProductType() ==  \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                ) {
                    if (in_array($item->getStatusId(), $expiredStatuses)) {
                        $giftcardItemsStatuses[$item->getId()] = $giftcardStatuses['expired'];
                    } else {
                        $giftcardItemsStatuses[$item->getId()] = $giftcardStatuses['avail'];
                    }
                }
            }
        } elseif ($order->getState() == \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT) {
            $status = $giftcardStatuses['payment_pending'];
        } elseif ($order->getState() == \Magento\Sales\Model\Order::STATE_PAYMENT_REVIEW) {
            $status = $giftcardStatuses['payment_review'];
        } else {
            $availableStatuses = [$orderItemStatusToEnable, \Magento\Sales\Model\Order\Item::STATUS_INVOICED];
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                    || $item->getRealProductType() ==\Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                ) {
                    if ($item->getStatusId() == \Magento\Sales\Model\Order\Item::STATUS_BACKORDERED
                        && $orderItemStatusToEnable == \Magento\Sales\Model\Order\Item::STATUS_PENDING
                        && !in_array(
                            \Magento\Sales\Model\Order\Item::STATUS_BACKORDERED,
                            $availableStatuses,
                            true
                        )
                    ) {
                        $availableStatuses[] = \Magento\Sales\Model\Order\Item::STATUS_BACKORDERED;
                    }

                    if (in_array($item->getStatusId(), $availableStatuses)) {
                        $giftcardItemsStatuses[$item->getId()] = $giftcardStatuses['avail'];
                    }
                }
            }
        }
        if (!$giftcardItemsStatuses && $status) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                    || $item->getRealProductType() ==  \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD
                ) {
                    $giftcardItemsStatuses[$item->getId()] = $status;
                }
            }
        }

        if ($giftcardItemsStatuses) {
            $giftcardPurchased = $this->_createItemsCollection()->addFieldToFilter(
                'order_item_id',
                ['in' => array_keys($giftcardItemsStatuses)]
            );

            foreach ($giftcardPurchased as $giftcardPurchasedItem) {
                if ($giftcardPurchasedItem->getStatus() != $giftcardStatuses['expired']
                    && !empty($giftcardItemsStatuses[$giftcardPurchasedItem->getOrderItemId()])
                ) {
                    $giftcardPurchasedItem->setStatus($giftcardItemsStatuses[$giftcardPurchasedItem->getOrderItemId()])->save();

                    //generate corresponding gift card
                    $order_item_id = $giftcardPurchasedItem->getData('order_item_id');
                    $generatedGiftCard = intval($this->_helper->getAmountGeneratedGiftCard($order_item_id));

                    /** @var \Magento\Sales\Model\Order\Item $orderItem */
                    $orderItem = $this->orderItemFactory->create()->load($order_item_id) ;
                    $orderedQty = (int)$orderItem->getQtyOrdered();

                    if ($generatedGiftCard < $orderedQty ) $this->generateGiftCard($giftcardPurchasedItem);
                }
            }
        }

        return $this;
    }

    private function _createItemsCollection()
    {
        return $this->_giftCardPurchasedFactory->create()->getCollection();
    }

    private function generateGiftCard($giftcardPurchased = 0, $code = null)
    {
        $sendMailLater = false;
        $pattern = $this->_scopeConfig->getValue(
            \Magenest\GiftCard\Model\GiftCard::XML_PATH_PATTERN
        );
        if (!$pattern) {
            $pattern ="[A4][N5]";
        }
        if (!$code) {
            $code = $this->generateCode($pattern) ;
        }

        //todo : calculate the date_expired for gift card
        $today = new \DateTime();

        $giftCardData= [
            'giftcard_purchased_id'=>$giftcardPurchased->getId(),
            'code'=>trim($code),
            'status'=>$giftcardPurchased->getStatus(),
            'template'=>$giftcardPurchased->getTemplate(),
            'personal_design'=>$giftcardPurchased->getPersonalDesign(),
            'date_expired'=>'',
            'website_id'=>$giftcardPurchased->getData('website_id'),
            'store_id'=>$giftcardPurchased->getData('store_id'),
            'order_item_id'=> $giftcardPurchased->getData('order_item_id'),
            'balance'=>$giftcardPurchased->getData('amount'),
            'state'=>'',
            'giftcard_image'=>'',
            'sender_name' => $giftcardPurchased->getData('sender_name') ,
            'sender_email' => $giftcardPurchased->getData('sender_email') ,
            'headline' =>$giftcardPurchased->getData('headline') ,
            'recipient_name'=>$giftcardPurchased->getData('recipient_name') ,
            'recipient_email'=>$giftcardPurchased->getData('recipient_email') ,
            'message' =>$giftcardPurchased->getData('message') ,
            'schedule_send_time' =>$giftcardPurchased->getData('schedule_send_time'),
            'giftcard_pdf' => ''
        ];

        //set the expiry date up
        $expiryDate = $this->calculateExpiryDate($giftcardPurchased->getData('expired_after_x_days'),$giftcardPurchased->getData('schedule_send_time'));
        $giftCardData['date_expired'] = $expiryDate;

        //calculating the first day of availability
        $firstDayAvail = $this->calFirstDayofAvailability($giftcardPurchased->getData('schedule_send_time'));

        $giftCardData['available_date'] = $firstDayAvail;

        //if today is not available date then the gift card status is 0

        if ($firstDayAvail) {
            $firstDayDateObj = new \DateTime($firstDayAvail);
            $today = new \DateTime();

            if ($today < $firstDayDateObj) {
                $giftCardData['status'] = 0;
                $sendMailLater = true;
            }
        }

        $giftcard = $this->_giftCardFactory->create()->setData($giftCardData)->save();

        //generate gift card pdf

        try {
            $pdf = $this->_helper->generatePdf($giftcard,null,true);

            if (isset($pdf['file_path']))  {
                $pdfPath = $pdf['file_path'];
                $giftcard->addData(['giftcard_pdf' => $pdfPath ])-> save();

                if (\Magenest\GiftCard\Helper\Data::ENABLE_DEBUG ) {
                    $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
                    $logger =  $objectManager->create('Magenest\GiftCard\Logger\Logger') ;
                    $logger->addDebug('pdf filepath');
                    $logger->addDebug($pdfPath);
                }
            }
        } catch (\Exception $e) {

            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $logger =  $objectManager->create('Magenest\GiftCard\Logger\Logger') ;
            $logger->addDebug('exception during generation of pdf');
            $logger->addDebug($e->getMessage());
            $logger->addDebug($e->getTraceAsString());

            $this->_logger->critical($e->getMessage());
            $this->_helper->sendEmail($giftcard);
        }

        if (!$sendMailLater)
        $this->_helper->sendEmail($giftcard);

        //send the email to the recipient
    }

    /**
     * calculating the first day that gift card will be availability
     * @param string $scheduleSentTime
     * @return string
     */
    public function calFirstDayofAvailability($scheduleSentTime)
    {
      if (!$scheduleSentTime) {
          $availableDateObj = new \DateTime();
      } else {
          $availableDateObj = new \DateTime($scheduleSentTime);
      }
       return $availableDateObj->format('Y-m-d');
    }

    /**
     * calculate the expiry date of gift card
     * @param string $expiry_after_x_day
     * @return string || null
     */
    public function calculateExpiryDate($expiry_after_x_day,$schedule_send_time)
    {
        /**
         * case 1: expired after 60 day;
         * scheduled send time :
         * today is June 1
         *
         */
        $expiry_after_x_day = intval($expiry_after_x_day);

        if ($expiry_after_x_day == 0) return null;

        if ($schedule_send_time) {
            $sent_time_obj = new \DateTime($schedule_send_time);
        } else {
            $sent_time_obj = new \DateTime();
        }

        if ($expiry_after_x_day == '1') {
            $expiryDateObj = $sent_time_obj->modify('+ 1 day');
        } else {
            $modified_string = '+ ' . $expiry_after_x_day  . ' days';
            $expiryDateObj = $sent_time_obj->modify($modified_string);
        }

        return $expiryDateObj->format('Y-m-d');
    }

    /**
     * analysing pattern of coupon which is defined by admin in setting panel
     *
     * @param string $pattern
     * @return string
     */
    public function generateCode($pattern)
    {
        $gen_arr = array ();

        preg_match_all("/\[[AN][.*\d]*\]/", $pattern, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $delegate = substr($match [0], 1, 1);
            $length = substr($match [0], 2, strlen($match [0]) - 3);
            if ($delegate == 'A') {
                $gen = $this->generateString($length);
            } elseif ($delegate == 'N') {
                $gen = $this->generateNum($length);
            }

            $gen_arr [] = $gen;
        }

        foreach ($gen_arr as $g) {
            $pattern = preg_replace('/\[[AN][.*\d]*\]/', $g, $pattern, 1);
        }
        return $pattern;
    }

    public function generateString($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $rand = '';
        for ($i = 0; $i < $length; $i ++) {
            $rand .= $c [rand() % strlen($c)];
        }

        return $rand;
    }

    /**
     * generate arbitratry string contain number digit
     *
     * @param int $length
     * @return string
     */
    public function generateNum($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $c = "0123456789";
        $rand = '';
        for ($i = 0; $i < $length; $i ++) {
            $rand .= $c [rand() % strlen($c)];
        }
        return $rand;
    }
}
