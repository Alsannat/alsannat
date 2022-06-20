<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/01/2016
 * Time: 10:18
 */
namespace Magenest\GiftCard\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class RemoveRedirect extends Add
{
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
        if (isset($params['giftcard_code'])) {
            $this->giftCard->removeFromCart($params['giftcard_code']);
            $this->cart->getQuote()->collectTotals()->save();
        }

        return $resultRedirect->setPath('checkout/cart/');
    }
}
