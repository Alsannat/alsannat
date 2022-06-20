<?php
namespace Alsannat\Varnish\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class PaymentMethodAvailable implements ObserverInterface
{
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cart = $objectManager->get('\Magento\Checkout\Model\Cart'); 
        $result = $observer->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        $quote = $observer->getQuote();

        if ($quote === null || $method_instance->getCode() != 'cashondelivery')
            return;

        // get cart items
        $items = $cart->getItems();

        // get attribute value of cart items
        foreach ($items as $item) 
        {
            $p = $objectManager->get('Magento\Catalog\Model\Product')->load($item['product_id']);
            $attribute = $p->getResource()->getAttribute('cod'); 
            if ($item->getProductType() == 'mpgiftcard') {
                //echo "Hi";exit;
                //print_r($result->getData());exit;
                $result->setData('is_available', 0);
                //print_r($result->getData());exit;
                break;
            }

        }

    }
}