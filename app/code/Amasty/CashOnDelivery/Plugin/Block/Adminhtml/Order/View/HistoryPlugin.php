<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Plugin\Block\Adminhtml\Order\View;

use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Sales\Block\Adminhtml\Order\View\History;

class HistoryPlugin
{
    /**
     * @param History $object
     * @param array $result
     *
     * @return array
     */
    public function afterGetStatuses(History $object, $result)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $object->getOrder();
        $paymentMethod = $order->getPayment()->getMethod();

        if ($paymentMethod === Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE && is_array($result)) {
            $result['processing'] = 'Processing';
        }

        return $result;
    }
}
