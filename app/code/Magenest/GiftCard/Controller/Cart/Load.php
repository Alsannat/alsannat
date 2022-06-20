<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/08/2016
 * Time: 15:15
 */

namespace Magenest\GiftCard\Controller\Cart;

use Magento\Framework\App\Action\Context;

use Magento\Framework\Controller\ResultFactory;

class Load extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magenest\GiftCard\Model\GiftCard
     */
    protected $giftCard;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /** @var \Magento\Checkout\Model\Session  */
    protected $checkoutSession;
    /**
     * @param Context $context
     * @param \Magenest\GiftCard\Model\GiftCard $giftCard
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     */
    public function __construct(
        Context $context,
        \Magenest\GiftCard\Model\GiftCard $giftCard,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
    
        parent::__construct($context);
        $this->giftCard = $giftCard;
        $this->checkoutSession = $checkoutSession;
        $this->quote = $this->checkoutSession->getQuote();
    }

    /**
     *get the json string that represent the gift card in quote
     */
    public function execute()
    {
        $out = [];
        //remove invalid gift cards first
        /** @var \Magento\Quote\Model\Quote $quote */

        $this->removeInvalidGiftCards();
        $giftcards = unserialize($this->quote->getData('gift_cards'));

        if ($giftcards) {
            foreach ($giftcards as $giftcard) {
                if ($giftcard['code']) {
                    $out[]=$giftcard;

                }
            }
        }

        $result =[];
        if (isset($out[0]['code'])) {
            $result['giftcard'] = $out[0]['code'];
        }
        /*
          * @var \Magento\Framework\Controller\Result\Json $resultJson
        */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }

    public function removeInvalidGiftCards()
    {
        $gift_cards = unserialize($this->quote->getData('gift_cards'));
        $removed_gift_card = [];

        if (!empty($gift_cards)) {
            foreach ($gift_cards as $item) {
                $code = $item['code'];
                $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

                /** @var \Magenest\GiftCard\Model\GiftCard  $giftCardModel */
                $giftCardModel =  $objectManager->create('Magenest\GiftCard\Model\GiftCard')->loadByCode($code);
                $isValid = $giftCardModel->isValid();

                if (isset($isValid['code']) && $isValid['code'] == 'error') {
                    $removed_gift_card[] = $code;
                }
            }
        }

        if (!empty($removed_gift_card)) {
            foreach ($gift_cards as $key=> $item) {
                foreach ($removed_gift_card as $removed_code) {
                    if ($removed_code == $item['code']) {
                        unset($gift_cards[$key]);
                    }
                }
            }

            $storedGiftCards = serialize($gift_cards);
            $this->quote->setIsChanged(1);
            $this->quote->setGiftCards($storedGiftCards)->save();
        }
    }
}
