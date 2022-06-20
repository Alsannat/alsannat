<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 08/05/2017
 * Time: 11:21
 */

namespace Magenest\GiftCard\Controller\Design;

use Magento\Framework\App\ResponseInterface;

class Mi extends \Magento\Framework\App\Action\Action
{
    protected $helper ;
    protected $cart;
    protected $checkoutSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\GiftCard\Helper\Cart $cart,
        \Magenest\GiftCard\Helper\Data $data,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->helper = $data;
        $this->cart = $cart;
        parent::__construct($context);
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $requestParam = $this->getRequest()->getParams();
        $this->cart->addToCart($requestParam);

        //get the quote id of current session
        $quoteId = $this->checkoutSession->getQuoteId();
        $this->getResponse()->setHeader('Access-Control-Allow-Origin', '*');
        $this->getResponse()->setBody($quoteId);
        $this->getResponse()->sendHeaders();
        $this->getResponse()->sendResponse();
    }
}
