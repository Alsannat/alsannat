<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 12/05/2017
 * Time: 22:42
 */

namespace Magenest\GiftCard\Observer\Quote;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * This class observe event an item is added into the quote
 * Class Item
 * @package Magenest\GiftCard\Observer\Quote
 */
class Item implements ObserverInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    //Magento\Checkout\Model
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $item \Magento\Quote\Model\Quote\Item */

        $item = $observer->getEvent()->getItem();
        $itemId = $item->getId();
        $this->checkoutSession->setLastAddedItemId($itemId);
        return $this;
    }
}
