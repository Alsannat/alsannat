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

class AddRedirect extends \Magento\Framework\App\Action\Action
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
    public function __construct(
        Context $context,
        \Magenest\GiftCard\Model\GiftCard $giftCard,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Helper\Cart $cartHelper
    ) {
    
        parent::__construct($context);
        $this->giftCard = $giftCard;
        $this->cart = $cart;
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
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $params = $this->getRequest()->getParams();

        $this->giftCard->addToCart($params['giftcard_code']);
        $this->cart->getQuote()->collectTotals()->save();

        return $resultRedirect->setPath('checkout/cart/');
    }
}
