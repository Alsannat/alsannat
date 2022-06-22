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

class Remove extends Add
{
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $result = [];
        try {
            $params = $this->getRequest()->getParams();
            if (isset($params['giftcard_code'])) {
                $this->giftCard->removeFromCart($params['giftcard_code']);
                $this->cart->getQuote()->collectTotals()->save();
                $result['code'] = 'ok';
                $result['message'] = __('You removed giftcard successfully');
            }
        } catch (\Exception $e) {
            $result['code'] = 'error';
            $result['message'] = __('You removed giftcard unsuccessfully');
        }

        /* @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($result);
        return $resultJson;
    }
}
