<?php

namespace Alsannat\Varnish\Observer;

use Magento\Framework\Event\ObserverInterface;

class Productsaveafter implements ObserverInterface
{    
    protected $request;

    public function __construct(
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $observer->getProduct();

        $productParent = null;
        
        if ($product->getTypeId() == "simple") {
            $parentIds = $objectManager->get('Magento\ConfigurableProduct\Model\Product\Type\Configurable')
                ->getParentIdsByChild($product->getId());

            $parentId = array_shift($parentIds);
            $productParent = $objectManager->create('Magento\Catalog\Model\Product')->load($parentId);
        }

        if ($productParent != null){
            $product = $productParent;
        }

        if ($product->getTypeId() == "configurable")
        {

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