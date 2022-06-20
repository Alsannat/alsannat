<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 22/01/2016
 * Time: 16:55
 */

namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Layout implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $layout = $observer->getEvent()->getLayout();
        $handler='catalog_product_view_type_giftcard';
        $layout->getUpdate()->addHandle($handler);
    }
}
