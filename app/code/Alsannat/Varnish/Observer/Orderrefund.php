<?php

namespace Alsannat\Varnish\Observer;

use Magento\Framework\Event\ObserverInterface;

class Orderrefund implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getCreditmemo()->getOrder();
        $order_items = $order->getAllItems();
        if ($order_items) {
            foreach ($order_items as $item)
            {
                $product = $item->getProduct();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $base_url = $storeManager->getStore()->getBaseUrl();
                $path = $product->getUrlKey();
                $messageManager = $objectManager->create('\Magento\Framework\Message\ManagerInterface');
                $messageManager->addSuccess(
                    __('Varnish URL has been purged.')
                );
                $url = str_replace('http://', '', $base_url);
                $url = str_replace('https://', '', $url);
                $url = str_replace('/', '', $url);

                $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == '.$url.' && req.url == /' . $path . '"';

                $results = exec($query . ' 2>&1');

                if ($path) {
                    $overall_url = $base_url . '/' . $path;
                } else {
                    $overall_url = $base_url;
                }
                $messageManager->addSuccess(
                    __($overall_url)
                );
            }
        }
    }
}