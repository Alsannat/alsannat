<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/01/2016
 * Time: 05:44
 */

namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Option implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        $product->setHasOptions(true);
    }
}
