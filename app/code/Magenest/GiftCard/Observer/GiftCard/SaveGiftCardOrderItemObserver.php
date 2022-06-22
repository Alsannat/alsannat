<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 24/02/2016
 * Time: 09:48
 */

namespace Magenest\GiftCard\Observer\GiftCard;

use Magento\Backend\Block\Widget\Grid\Column\Filter\Date;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveGiftCardOrderItemObserver implements ObserverInterface
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

    /** @var  \Magenest\GiftCard\Model\GiftCardFactory */
    protected $_giftCardFactory;

    /** @var  \Magenest\GiftCard\Model\GiftCardPurchased */
    protected $_giftCardPurchasedFactory;

    /** @var \Magenest\GiftCard\Helper\Data  */
    protected $_helper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magenest\GiftCard\Model\GiftCardPurchasedFactory $giftCardPurchasedFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\GiftCard\Helper\Data $helper,
        \Magento\Framework\DataObject\Copy $objectCopyService
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_productFactory = $productFactory;
        $this->_giftCardPurchasedFactory = $giftCardPurchasedFactory;
        $this->_objectCopyService = $objectCopyService;

        $this->_helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $observer->getEvent()->getItem();
        $this->createGiftCardPurchased($orderItem);
        return $this;
    }

    /**
     * @param $orderItem \Magento\Sales\Model\Order\Item
     * @return $this
     */
    public function createGiftCardPurchased($orderItem)
    {
        $personal_design = '';
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        if (!$orderItem->getId()) {
            //order not saved in the database
            return $this;
        }
        if ($orderItem->getProductType() != \Magenest\GiftCard\Model\Product\Type\GiftCard::TYPE_GIFTCARD) {
            return $this;
        }
        $product = $orderItem->getProduct();

        //check if there is a existing record of gift card for the item then return . Otherwise, the module will create number of records corresponding with the qty

        $purchasedGiftCardCollection = $this->_giftCardPurchasedFactory->create()->getCollection()->addFieldToFilter('order_item_id', $orderItem->getId());

        if ($purchasedGiftCardCollection->getSize() > 0) {
            //return $this;
        }

        //there is no record , then create it/them

        $order_id = $orderItem->getOrderId();
        $option = $orderItem->getProductOptions();

        if (isset($option['additional_options']) || isset($option['info_buyRequest']['giftcard'])) {
            if (isset($option['additional_options'])) {
                $additional_options = $option['additional_options'];
            } elseif (isset($option['info_buyRequest']['giftcard'])) {
                $additional_options = $option['info_buyRequest']['giftcard'];
                //the personal design
            }

            //getQuoteItemId
            $quoteItemId = $orderItem->getQuoteItemId();
            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

            /** @var  $quoteItem \Magento\Quote\Model\Quote\Item */
            $quoteItem = $objectManager->get('Magento\Quote\Model\Quote\Item')->load($quoteItemId);

            /** @var  $giftcardOption \Magento\Quote\Model\Quote\Item\Option */
            $giftcardOption = $objectManager->get('Magento\Quote\Model\Quote\Item\Option')->getCollection()
                ->addFieldToFilter('item_id',$quoteItemId)->addFieldToFilter('code','giftcard_design')->getFirstItem();

            $giftCardDesignInfo = [];
            if (is_object($giftcardOption)) $giftCardDesignInfo = unserialize($giftcardOption->getValue());

            if (isset($giftCardDesignInfo[0])) {
                $personal_design = $giftCardDesignInfo[0]['value'];
            }

            $giftCardPurchasedData = array();
            $giftcardInfomation = [
                'amount',
                'template',
                'recipient_name',
                'recipient_email',
                'sender_name',
                'sender_email',
                'headline',
                'message',
                'schedule_send_time'
            ];

            foreach ($giftcardInfomation as $giftcardInfo) {
                foreach ($additional_options as $key => $add_option) {
                    if (isset($add_option['label'])) {
                        if ($add_option['label'] == __('giftcard_' . $giftcardInfo)) {
                            $giftCardPurchasedData[$giftcardInfo] = $add_option['value'];
                        }
                    } else {
                        if ($key == __('giftcard_' . $giftcardInfo)) {
                            $giftCardPurchasedData[$giftcardInfo] = $add_option;
                        }
                    }

                    if (isset($additional_options['giftcard_template'])) $giftCardPurchasedData['template'] = $additional_options['giftcard_template'];
                }
            }

            $giftCardPurchasedData['order_item_id'] =$orderItem->getId();

            if (isset($giftCardPurchasedData['schedule_send_time'])) {
                if ($giftCardPurchasedData['schedule_send_time'])  {
                    $dateObj =  \DateTime::createFromFormat('d/m/Y',$giftCardPurchasedData['schedule_send_time']);
                    //$giftCardPurchasedData['schedule_send_time'] = date('Y-m-d H:i:s' ,$giftCardPurchasedData['schedule_send_time']);
                    $giftCardPurchasedData['schedule_send_time'] = $dateObj->format('Y-m-d H:i:s');
                }

            }

            $giftCardPurchasedData['expired_after_x_days'] =(int)$product->getData('giftcard_expired_after');

            $giftCardPurchasedData['store_id'] = $orderItem->getStoreId();
            $order = $orderItem->getOrder();
            $giftCardPurchasedData['website_id'] =  $order->getStore()->getWebsiteId();

            $giftCardPurchasedData['status'] =\Magenest\GiftCard\Model\GiftCardPurchased::STATUS_PENDING;

            $giftCardPurchasedData['personal_design'] = $personal_design;

            //default gift card balance
            if (!isset($giftCardPurchasedData['amount'])) $giftCardPurchasedData['amount'] ='';
            if (!$giftCardPurchasedData['amount']) $giftCardPurchasedData['amount'] = $product->getPrice();

            $orderQty = intval($orderItem->getQtyOrdered());
            $generatedGiftCardPurchased = intval($this->_helper->getAmountGeneratedGiftCardPurchased($orderQty));

            if ($generatedGiftCardPurchased < $orderQty) {
                for ($i = 0; $i < $orderQty; $i++) {
                    $this->_giftCardPurchasedFactory->create()->setData($giftCardPurchasedData)->save();
                }
            }
        }
    }
}
