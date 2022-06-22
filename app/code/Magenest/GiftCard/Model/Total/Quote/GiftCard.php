<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 30/11/2015
 * Time: 16:54
 */
namespace Magenest\GiftCard\Model\Total\Quote;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Store\Model\Store;
use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;

class GiftCard extends AbstractTotal
{

    /** @var  array of gift card that customer applied */
    protected $_giftCard ;

    /** @var  array of gift card that customer applied */

    protected $_usedGiftCard;

    /** @var  \Magenest\GiftCard\Model\Calculator */
    protected $_calculator;
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $logger;

    /**
     * @param \Magento\Weee\Helper\Data $weeeData
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magenest\GiftCard\Helper\Data $giftcardData,
        \Magenest\GiftCard\Model\Calculator $caculator,
        \Magenest\GiftCard\Logger\Logger $logger,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->eventManager = $eventManager;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->_helper = $giftcardData;

        $this->_calculator= $caculator;
        $this->logger = $logger;
        $this->setCode('giftcard');
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        AbstractTotal::collect($quote, $shippingAssignment, $total);


        $store = $this->storeManager->getStore($quote->getStoreId());
        $address = $shippingAssignment->getShipping()->getAddress();
        $items = $shippingAssignment->getItems();

        $websiteId = $store->getWebsiteId();

        //if there is no item in shopping cart then return
        if (!count($items)) {
            return $this;
        }

        if ($quote) {
            $giftcards = unserialize($quote->getData('gift_cards'));
            $this->logger->addDebug('in the Model/Total/Quote/GiftCard');
            $this->logger->addDebug('quote  '. $quote->getId() . ' has gift cards');
            $this->logger->addDebug($quote->getData('gift_cards'));

            if ($giftcards) {
                $this->_calculator->collectQuoteGiftCards($quote, $total);
                $baseTotalAmount = $this->_calculator->getBaseAmount();

                $this->logger->addDebug('base gift card amount ' . $baseTotalAmount);

                $convertedTotal = $this->priceCurrency->convertAndRound($baseTotalAmount);
                if ($baseTotalAmount) {
                    $total->setBaseTotalAmount($this->getCode(), $baseTotalAmount);
                    $total->setTotalAmount($this->getCode(), $convertedTotal);

                    $quote->setData('base_gift_cards_amount_used', $baseTotalAmount);
                    $quote->setData('gift_cards_amount_used', $convertedTotal);


                    //save the original grand total

                    $quote->setData('base_grand_total_without_giftcard', $quote->getData('base_grand_total'));
                    $quote->setData('grand_total_without_giftcard', $quote->getData('grand_total'));

                    $quoteBaseGrandTotal = max(0, $quote->getData('base_grand_total')- $baseTotalAmount);
                    $quoteGrandTotal = max(0, $quote->getData('grand_total')- $convertedTotal);
                    $quote->setData('grand_total', $quoteBaseGrandTotal);
                    $quote->setData('base_grand_total', $quoteGrandTotal);


                    $quote->save();
                    $total->addTotalAmount($this->getCode(), -$convertedTotal);
                    $total->setTotalAmount('grand', $total->getGrandTotal()-$convertedTotal);
                    $total->setBaseTotalAmount('grand', $total->getBaseGrandTotal() -$baseTotalAmount);

                    $total->setData('grand_total', $total->getGrandTotal()-$convertedTotal);
                    $total->setData('base_grand_total', $total->getBaseGrandTotal() -$baseTotalAmount);

                    $address->setData('gift_cards_amount', $convertedTotal)->setData('base_gift_cards_amount', $baseTotalAmount)->save();
                }
            } else {
                $quote->setData('base_gift_cards_amount_used', 0);
                $quote->setData('gift_cards_amount_used', 0);

                //remove the total gift card value
                /*$quote->setData('base_gift_cards_amount_used',0);
                $quote->setData('gift_cards_amount_used',0);
                $address->setData('gift_cards_amount', 0)->setData('base_gift_cards_amount',0)->save();

                if ($quote->getData('base_grand_total_without_giftcard')) {
                    $quote->setData('base_grand_total', $quote->getData('base_grand_total_without_giftcard'));
                    $quote->setData('grand_total', $quote->getData('grand_total_without_giftcard'));
                }
                $quote->save();*/
            }
        }

        return $this;
    }

    /**
     * Add giftcard total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $quoteId = $quote->getId();
        $totalId = $total->getId();
        $result = null;


        $amount = $quote->getData('gift_cards_amount_used');

        if ($amount != 0) {
            $description = $total->getGiftcardAmountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' =>  __('Gift Card'),
                'value' => $amount,
                'area' => 'tax',
            ];
        }
        return $result;
    }
}
