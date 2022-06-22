<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/03/2016
 * Time: 15:50
 */

namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Attribute implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $response = $observer->getEvent()->getResponse();
        if ($response->getTypes()) {
            $types = $response->getTypes();
        } else {
            $types = array();
        }
        $types['giftcard_template']='Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\Template';
        $types['giftcard_price_selector']='Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\PriceSelector';
        $response->setTypes($types);
    }
}
