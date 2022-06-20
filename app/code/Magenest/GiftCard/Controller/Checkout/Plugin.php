<?php

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 25/07/2017
 * Time: 09:15
 */
namespace Magenest\GiftCard\Controller\Checkout;
use Magento\Framework\Exception\LocalizedException;
class Plugin
{
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;
    protected $resultRedirectFactory ;
    protected $messageManager;
    /**
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(\Magento\Checkout\Model\Cart $cart,
                                \Magento\Framework\App\Action\Context $context
    )
    {
        $this->cart = $cart;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->messageManager = $context->getMessageManager();

    }

    /**
     * Disable multishipping
     *
     * @param \Magento\Framework\App\Action\Action $subject
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(\Magento\Framework\App\Action\Action $subject)
    {
        // $this->cart->getQuote()->setIsMultiShipping(0);
        $params = $subject->getRequest()->getParams();

        if (isset($params['giftcard']['giftcard_schedule_send_time'])) {
            $scheduledSendTime = $params['giftcard']['giftcard_schedule_send_time'];

            try {
                //$date  = strtotime($scheduledSendTime);
                //$scheduledDate = date_create_from_format('D, d M Y', $scheduledSendTime);
                $scheduledDate = date_create_from_format('d/m/Y', $scheduledSendTime);
            } catch (\Exception $e) {

            }
            if (is_object($scheduledDate)) {
                $today = new \DateTime();

                $interval = $today->diff($scheduledDate);
                $day_str = $interval->format('%R%a');
                $compare = $interval->format('%R');
                $day = floatval($interval->format('%R%a'));

                if ($compare == '-') {
                    //throw new LocalizedException(__('The scheduled send date is invalid'));
                    // $this->resultRedirectFactory = $subject->getContext()->getResultRedirectFactory();
                    $this->messageManager->addNotice(
                        __('The scheduled send date is invalid')
                    );
                    $subject->getRequest()->setParam('form_key', '');
                    // return $this->resultRedirectFactory->create()->setPath('*/*/');
                    // exit();

                    //return $subject->goBack();
                }
            }
        }

    }

}