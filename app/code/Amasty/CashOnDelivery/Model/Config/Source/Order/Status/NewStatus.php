<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CashOnDelivery
 */


namespace Amasty\CashOnDelivery\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Order;

/**
 * Order Statuses source model
 */
class NewStatus extends \Magento\Sales\Model\Config\Source\Order\Status\NewStatus
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_push($options, ['value' => Order::STATE_PROCESSING, 'label' => 'Processing']);

        return $options;
    }
}
