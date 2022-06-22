<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/01/2016
 * Time: 10:17
 */
namespace Magenest\GiftCard\Controller\Cart;

use Magento\Framework\App\ResponseInterface;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $cartHelper;
    /**
     * @var \Magenest\GiftCard\Model\GiftCard
     */
    protected $giftCard;

    protected $quote;
    public function __construct(
        Context $context,
        \Magenest\GiftCard\Model\GiftCard $giftCard,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
    
        parent::__construct($context);
        $this->giftCard = $giftCard;
        $this->cart = $cart;
        $this->quote = $cart->getQuote();
        $this->cartHelper  = $cartHelper;
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        $result = [ ];
        try {
            $giftCardCode = $params['giftcard_code'];

            $giftCardModel = $this->giftCard->loadByCode($giftCardCode);

            if (!$giftCardModel->getId()) {
                $result['code'] = 'error';
                $result['message'] = __('The gift card code is not exist');
                /*
                * @var \Magento\Framework\Controller\Result\Json $resultJson
                */
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($result);
                return $resultJson;
            }

            //whether the gift card valid
            $isValid  = $giftCardModel->isValid();

            if (!$isValid['is_valid']) {
                $result['code'] = 'error';
                $result['message'] = $isValid['message'];
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($result);
                return $resultJson;
            }
            //whether the gift card is applied for current quote

            $currentAppliedGiftCards = unserialize($this->quote->getData('gift_cards'));

            //version

            if (!empty($currentAppliedGiftCards)) {
                $result['code'] = 'error';
                $result['message'] = __('you can apply one gift card only for a cart');
                /*
                * @var \Magento\Framework\Controller\Result\Json $resultJson
                */
                $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $resultJson->setData($result);
                return $resultJson;
            }
            if (!empty($currentAppliedGiftCards))  {
                foreach ($currentAppliedGiftCards as $item) {
                    $code = $item['code'];
                    if ($giftCardCode == $code) {
                        $result['code'] = 'error';
                        $result['message'] = __('The gift card code is already applied for this cart');
                        /*
                        * @var \Magento\Framework\Controller\Result\Json $resultJson
                        */
                        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                        $resultJson->setData($result);
                        return $resultJson;
                    }
                }
            }

           // $this->giftCard->addToCart($params['giftcard_code']);

            if (empty($currentAppliedGiftCards)) {
                $giftcards = array();
            }

            $giftcards[] = ['code'=>$giftCardCode, 'id' =>$giftCardModel->getData('giftcard_id')];
            $this->quote->setData('gift_cards', serialize($giftcards))->save();

            $this->quote->setTotalsCollectedFlag(false);
            $this->cart->getQuote()->collectTotals()->save();
            $result['code'] = 'ok';
            $result['message'] = __('You applied giftcard successfully');
        } catch (\Exception $e) {
            $result['code'] = 'error';
            $result['message'] = __('You applied giftcard unsuccessfully');
        }
        /*
           * @var \Magento\Framework\Controller\Result\Json $resultJson
         */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}
