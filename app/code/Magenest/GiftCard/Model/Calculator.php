<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 16:09
 */

namespace Magenest\GiftCard\Model;

class Calculator
{

    protected $_isValid ;

    protected $_baseAmount = 0;
    protected $quoteGrandTotalLeft = 0;

    protected $_baseAmountUsed = 0;

    /** @var  \Magenest\GiftCard\Model\GiftCardFactory */
    protected $_giftCardFactory;

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

    /** @var  \Magenest\GiftCard\Model\GiftCardPurchased */
    protected $_giftCardPurchasedFactory;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magenest\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magenest\GiftCard\Model\GiftCardPurchasedFactory $giftCardPurchasedFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magenest\GiftCard\Logger\Logger $logger,
        \Magento\Framework\DataObject\Copy $objectCopyService
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_giftCardFactory = $giftCardFactory;
        $this->_productFactory = $productFactory;
        $this->_giftCardPurchasedFactory = $giftCardPurchasedFactory;
        $this->_objectCopyService = $objectCopyService;
        $this->logger = $logger;
    }

    public function validateGiftCard($giftCard)
    {
        return true;
    }

    /**
     * @param $quote \Magento\Quote\Model\Quote
     *
     */
    public function collectQuoteGiftCards($quote, $total)
    {
        $giftCardCollectedFlag = $quote->getGiftCardCollectedFlag();
        if (!$giftCardCollectedFlag) {
            $this->_baseAmount = 0;
            $this->_baseAmountUsed = 0;


            $subtotal = $total->getBaseTotalAmount('subtotal');
            $shipping =   $total->getBaseTotalAmount('shipping');
            $tax = $total->getBaseTotalAmount('tax');
            $weee =  $total->getBaseTotalAmount('weee');
            $wee_tax =  $total->getBaseTotalAmount('weee_tax');
            $discount =  $total->getBaseTotalAmount('discount');
            $discount_tax_compensation =$total->getBaseTotalAmount('discount_tax_compensation');
            $shipping_discount_tax_compensation =  $total->getBaseTotalAmount('shipping_discount_tax_compensation');
            $this->quoteGrandTotalLeft=  $subtotal
                +   $shipping
                +  $tax
                + $weee
                + $wee_tax

                - $discount
                - $discount_tax_compensation
                - $shipping_discount_tax_compensation

            ;

            $cards = unserialize($quote->getData('gift_cards'));
            if ($cards) {
                foreach ($cards as $k => $card) {
                    if (isset($card['code'])) {
                        $giftCard = $this->_giftCardFactory->create()->loadByCode(trim($card['code']));

                        $this->logger->addDebug(serialize($giftCard->getData()));

                        $isValidated = $this->validateGiftCard($giftCard);

                        if (!$isValidated) {
                            unset($cards[$k]);
                        } else {
                            $giftCardBalance =$giftCard->getBalance();

                            $this->logger->addDebug('card balance for '.$card['code'] . ' is '. $giftCardBalance );

                            if ($giftCardBalance < $this->quoteGrandTotalLeft) {
                                $this->_baseAmount +=$giftCard->getData('balance');
                                $card['used']= $giftCard->getData('balance');

                                $cards[$k]['used'] = $card['used'];
                                $this->quoteGrandTotalLeft = $this->quoteGrandTotalLeft-$giftCardBalance;
                            } elseif ($giftCardBalance ==$this->quoteGrandTotalLeft) {
                                $this->_baseAmount  +=$giftCard->getData('balance');
                                $this->quoteGrandTotalLeft = 0;
                                $card['used']= $giftCard->getData('balance');
                                $cards[$k]['used'] = $card['used'];
                            } elseif ($giftCardBalance > $this->quoteGrandTotalLeft) {
                                $this->_baseAmount += $this->quoteGrandTotalLeft;
                                $quoteGrandTotalLeft = 0;
                                $card['used'] = $this->quoteGrandTotalLeft;
                                $cards[$k]['used'] = $card['used'];
                            }
                            //  $this->_baseAmount +=$giftCard->getData('balance');
                        }
                    }
                }
            }

            $quote->setData('gift_cards', serialize($cards))->save();
            $quote->setGiftCardCollectedFlag(true);
        }
    }

    public function getBaseAmount()
    {
        return $this->_baseAmount;
    }

    public function getQuoteGrandTotal()
    {
        return $this->quoteGrandTotalLeft;
    }
    public function getAmountFromBaseAmount($storeId = 0)
    {
    }
}
