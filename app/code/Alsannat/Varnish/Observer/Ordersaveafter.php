<?php

namespace Alsannat\Varnish\Observer;

use Magento\Framework\Event\ObserverInterface;

class Ordersaveafter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $orderId = $observer->getEvent()->getOrderIds();
        $order = $objectManager->create('\Magento\Sales\Model\Order')->load($orderId);

        $order_items = $order->getAllItems();
        if ($order_items) {
            foreach ($order_items as $item)
            {
                $productRepository = $objectManager->create('\Magento\Catalog\Model\ProductRepository');
                $product = $productRepository->get($item->getSku());
                $productStockObj = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($product->getId());
                if ($productStockObj->getQty() <= 0) {
                    $product = $item->getProduct();
                    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                    $base_url = $storeManager->getStore()->getBaseUrl();
                    $path = $product->getUrlKey();
                    $messageManager = $objectManager->create('\Magento\Framework\Message\ManagerInterface');
                    $url = str_replace('http://', '', $base_url);
                    $url = str_replace('https://', '', $url);
                    $url = str_replace('/', '', $url);

                    $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == ' . $url . ' && req.url == /' . $path . '"';

                    $results = exec($query . ' 2>&1');

                    if ($path) {
                        $overall_url = $base_url . '/' . $path;
                    } else {
                        $overall_url = $base_url;
                    }
                }
            }
        }
    }
}