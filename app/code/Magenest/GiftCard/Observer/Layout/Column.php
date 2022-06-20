<?php

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 01/07/2017
 * Time: 12:07
 */
namespace Magenest\GiftCard\Observer\Layout;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class Column implements ObserverInterface
{
    public function array_swap($key1, $key2, $array) {
        $newArray = array ();
        foreach ($array as $key => $value) {
            if ($key == $key1) {
                $newArray[$key2] = $array[$key2];
            } elseif ($key == $key2) {
                $newArray[$key1] = $array[$key1];
            } else {
                $newArray[$key] = $value;
            }
        }
        return $newArray;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
       // $observer->getEvent()->getLayout()->getUpdate()->addHandle('ultimatefollowupemail_rule_new_ab');

        /** @var \Magento\Framework\View\Layout $layout */

        $layout = $observer->getEvent()->getLayout();
        /** @var \Magento\Sales\Block\Adminhtml\Order\View\Items $orderItemBlock */
        $orderItemBlock = $layout ->getBlock('order_items');

        if (is_object($orderItemBlock)) {

            $columns = $orderItemBlock->getColumns();
            //$columns = $this->array_swap('total', 'giftcard', $columns);

            //$orderItemBlock->setData('columns',$columns);

            /** @var \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer $defaultRenderer */
            $defaultRenderer = $orderItemBlock->getChildBlock('default');

            $columns = $defaultRenderer->getColumns();

            if (isset($columns['giftcard']) && !empty($columns))
            $columns = $this->array_swap('total', 'giftcard', $columns);

            $defaultRenderer->setData('columns',$columns);
            $childNames = $orderItemBlock->getChildNames();
        }

        return $this;
    }

}