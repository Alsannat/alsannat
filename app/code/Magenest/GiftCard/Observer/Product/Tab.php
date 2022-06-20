<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/11/2015
 * Time: 14:50
 */
namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Tab implements ObserverInterface
{

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $fullActionName = $observer->getEvent()->getFullActionName();
        $layout = $observer->getEvent()->getLayout();
        $handler = '';

        if ($fullActionName == 'adminhtml_user_edit') {
            $handler = 'booking_user_handler';
        } elseif ($fullActionName == 'catalog_product_new' || $fullActionName == 'catalog_product_edit') {
            $handler = 'giftcard_catalog_product_edit';
        }
        //if ($handler) $layout->getUpdate()->addHandle($handler);
    }
}
