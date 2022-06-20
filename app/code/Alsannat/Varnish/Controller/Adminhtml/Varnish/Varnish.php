<?php

namespace Alsannat\Varnish\Controller\Adminhtml\Varnish;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Index
 * @package Alsannat\Varnish\Controller\Adminhtml\Varnish
 */
class Varnish extends Action
{
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect('adminhtml/cache/index');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $base_url = $storeManager->getStore()->getBaseUrl();
        $path = $this->getRequest()->getParam('path');
        $this->messageManager->addSuccess(
            __('Varnish URL has been purged.')
        );
        $url = str_replace('http://', '', $base_url);
        $url = str_replace('https://', '', $url);
        $url = str_replace('index.php', '', $url);
        $url = str_replace('/', '', $url);

        if (substr($path, -1) == '*')
        {
            $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == '.$url.' && req.url ~ /' . $path . '"';
        } else {
            $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == '.$url.' && req.url == /' . $path . '"';
        }

        $results = exec($query . ' 2>&1');

        if ($path) {
            $overall_url = $base_url . $path;
        } else {
            $overall_url = $base_url;
        }

        $this->messageManager->addSuccess(
            __('Varnish URL Purged: '. $overall_url)
        );

        $this->purgeSecondaryUrl($base_url, $path);


    }
    public function purgeSecondaryUrl($url, $path){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $base_url = $storeManager->getStore()->getBaseUrl();

        $url = str_replace('http://', '', $url);
        $url = str_replace('https://', '', $url);
        $url = str_replace('index.php', '', $url);
        $url = str_replace('/', '', $url);
        $url = str_replace('au', '', $url);

        if (substr($path, -1) == '*')
        {
            $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == '.$url.' && req.url ~ /' . $path . '"';
        } else {
            $query = 'sudo varnishadm -T :6082 -S /etc/varnish/secret ban "req.http.host == '.$url.' && req.url == /' . $path . '"';
        }

        $results = exec($query . ' 2>&1');

        if ($path) {
            $overall_url = $url . '/' . $path;
        } else {
            $overall_url = $url;
        }

        $this->messageManager->addSuccess(
            __('Varnish URL Purged: '. $overall_url)
        );

    }

    public function purgeFastlyUrl($url, $path){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $base_url = $storeManager->getStore()->getBaseUrl();

        $query = 'sudo curl -X PURGE ' . $url.'/' . $path;

        $results = exec($query . ' 2>&1');

        if ($path) {
            $overall_url = $base_url . $path;
        } else {
            $overall_url = $base_url;
        }
        $this->messageManager->addSuccess(
            __('Fastly URL Purged: '. $overall_url)
        );

    }
}