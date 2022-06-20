<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:24
 */
namespace Magenest\GiftCard\Model;

class GiftCard extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const XML_PATH_PATTERN ="giftcard/general/pattern";
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_giftcard';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'giftcard';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magenest\GiftCard\Model\History
     */
    protected $giftCardHistory;

    /** @var \Magenest\GiftCard\Model\HistoryFactory  */
    protected $giftCardHistoryFactory;

    /** @var $helper \Magenest\GiftCard\Helper\Data */
    protected $helper ;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem\File\ReadFactory
     */
    protected $fileReadFactory;

    protected $logger;

    /**
     * constructor method
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\GiftCard\Model\ResourceModel\GiftCard $resource = null,
        \Magenest\GiftCard\Model\ResourceModel\GiftCard\Collection $resourceCollection = null,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magenest\GiftCard\Model\HistoryFactory $historyFactory,
        \Magenest\GiftCard\Helper\Data $helper,
        \Magenest\GiftCard\Logger\Logger $logger,
        \Magento\Framework\Filesystem\File\ReadFactory $fileReadFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->checkoutSession = $checkoutSession;
        $this->giftCardHistoryFactory = $historyFactory;
        $this->helper = $helper;
        $this->fileReadFactory = $fileReadFactory;
        $this->logger = $logger;
    }

    public function _construct()
    {

        $this->_init('Magenest\GiftCard\Model\ResourceModel\GiftCard');
    }

    public function afterLoad()
    {
        parent::afterLoad();

        $this->giftCardHistory =  $this->giftCardHistoryFactory->create()->getCollection()->addFieldToFilter('giftcard_id', $this->getId());

        return $this;
    }

    /**
     * check whether giftcard is valid
     * a gift card is valid if the code is exist in db, if is is not expiry yet and its status is active
     */
    public function isValid()
    {
       $output = [];
       if (!$this->getId()) {
           $output['is_valid']= false;
           $output['message']= __('Gift Card is not exist');
           return $output;
       } else {
           $isExpired = $this->isExpired();
           if ($isExpired) {
               $output['is_valid']= false;
               $output['message']= __('Gift Card is already expired');
               return $output;
           } else {
               $status = $this->getData('status');
               if ($status == 0) {
                   $output['is_valid']= false;
                   $output['message']= __('Gift Card is not active');
                   return $output;
               } else {
                   $balance = $this->getData('balance');

                   if (!($balance > 0))  {
                       $output['is_valid']= false;
                       $output['message']= __('Gift Card balance is not greater than zero');
                       return $output;
                   }
               }

           }

       }
        $output['is_valid'] = true;
       return $output;
    }

    /**
     * associate a gift card with a quote
     */
    public function addToCart($code)
    {
        $quote = $this->checkoutSession->getQuote();

        if ($this->isValidToAddToCart($quote, $code)) {
            $giftcards = unserialize($quote->getData('gift_cards'));
            if (!$giftcards) {
                $giftcards = array();
            }

                $giftcards[] = ['code'=>$code, 'id' =>$this->loadByCode($code)->getData('giftcard_id')];
                $quote->setData('gift_cards', serialize($giftcards))->save();

                $this->logger->addDebug('gift cards for quote '. $quote->getId() . ' '. serialize($giftcards) );

        } else {
            $this->logger->addDebug('gift card is invalid '. $code);
        }
    }

    /**
     * @param $code
     */
    public function removeFromCart($code)
    {
        $quote = $this->checkoutSession->getQuote();

            $giftcards = unserialize($quote->getData('gift_cards'));
        if (!$giftcards) {
            return;
        }
        foreach ($giftcards as $key => $giftcard) {
            if ($giftcard['code'] == $code) {
                unset($giftcards[$key]);
            }
        }
        $quote->setIsChanged(1);
        $quote->setGiftCards(serialize($giftcards))->save();
    }

    /**
     * validate the gift card
     * @param $quote \Magento\Quote\Model\Quote
     * @return bool
     */
    public function isValidToAddToCart($quote, $code)
    {
        $isValid = true;
        $giftcards = unserialize($quote->getData('gift_cards'));
        $this->logger->addDebug('the old serialized gift card '. $quote->getData('gift_cards'));

        if ($giftcards) {
            foreach ($giftcards as $giftcard) {
                if ($giftcard['code'] == $code) {
                    $this->logger->addDebug('the old gift card '. $code);
                    $isValid = true;
                }
            }
        }

        return $isValid;
    }

    /**
     * whether gift card is expiry yet
     */
    public function isExpired()
    {
        if ($this->getId()) {
            $today = new \DateTime();
            $date_expired = $this->getData('date_expired');

            $expiryDateObj = new \DateTime($date_expired);

            if ($expiryDateObj < $today) {
                return true;

            }

        }
        return false;
    }

    /**
     * @param $code
     * @return $this;
     */
    public function loadByCode($code)
    {
        $giftcard =  $this->getCollection()->addFieldToFilter('code', $code)->getFirstItem();
        return $giftcard;
    }

    /**
     * Reduce the balance of gift card
     * @param $amount int
     * @param $quote \Magento\Quote\Model\Quote
     * @param $order \Magento\Sales\Model\Order
     */
    public function payForOrder($amount, $quote, $order)
    {
        $this->setBalance($this->getData('balance') - $amount) ->save()  ;
        $this->giftCardHistory= $this->giftCardHistoryFactory->create();
        $this->giftCardHistory->setData(
            [   'history_id' => null,
                'giftcard_id' => $this->getId(),
                'action' => \Magenest\GiftCard\Model\History::PAY_FOR_ORDER,
                'amount' => $amount,
                'delta' => '-' . $amount,
                'quote' =>$quote->getId(),
                'order' =>$order->getId()
            ]
        )->save();
    }

    /**
     * send the gift card to recipient
     * @return $this
     */
    public function sendToRecipient()
    {
        return $this;
    }
    public function getName()
    {
        $data = $this->getData();
        if (!$data) {
            return 'giftcard';
        } else {
            $orderItemId = $data['order_item_id'];
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

            /** @var \Magento\Sales\Model\Order\Item $orderItem */
            $orderItem = $objectManager->get('Magento\Sales\Model\Order\Item')->load($orderItemId);
            $productName =  $orderItem->getProduct()->getName();

            if ($productName) {
                return $productName;
            } else {
                return 'giftcard';
            }
        }
    }

    /**
     * @return Zend_PDF
     */
    public function getPDF()
    {

        if (!$this->getData('giftcard_pdf')) {
            $pdf = $this->helper ->generatePdf($this,null,true);

            if (isset($pdf['file_path'])) {
                $this->setData('giftcard_pdf', $pdf['file_path'])->save();
            }
        }

        return $this->getData('giftcard_pdf');
    }

    /**
     * @return null or
     */
    public function getPersonalDesignBlob()
    {
        $filePath = $this->getData('personal_design');
        if ($filePath) {
            /** @var \Magento\Framework\Filesystem\File\Read $read */
            $read = $this->fileReadFactory->create($filePath, \Magento\Framework\Filesystem\DriverPool::FILE);
            $content = $read->readAll();
            return $content;
        }
    }

    /**
     *@return string || null
     */
    public function getPersonalDesignUrl()
    {
        $filePath = $this->getData('personal_design');

        $regularExp = "/.*media\/(.*)/";
        preg_match($regularExp,$filePath,$matches);

        if (isset($matches[1])) {
            return $matches[1];
        }
    }

    /**
     *@return string || null
     */
    public function getPdfDesignUrl()
    {
        $filePath = $this->getData('giftcard_pdf');

        $regularExp = "/.*media\/(.*)/";
        preg_match($regularExp,$filePath,$matches);

        if (isset($matches[1])) {
            return $matches[1];
        }
    }

    /**
     *Get Balance with Currency Symbol
     */
    public function getBalanceWithCurrencyCode()
    {
        $storeId = $this->getStoreId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');

        if ($storeId) {
            $currencyCode = $storeManager->getStore($storeId)->getBaseCurrency()->getCurrencySymbol();
        } else {
            $currencyCode = $storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        }

        return $this->getBalance(). ' '. $currencyCode;

    }

}
