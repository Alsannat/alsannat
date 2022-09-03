<?php

namespace Wigzo\TagManager\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    private function serviceWorker () {
        $scopeInterface = $this->_objectManager->create ('\Magento\Framework\App\Config\ScopeConfigInterface');

        $enabled = $scopeInterface->getValue ("admin/wigzo/enabled");
        $orgId = $scopeInterface->getValue ("admin/wigzo/orgId");

        if ($enabled != "true") {
            $this->getResponse()->clearHeaders()->setHeader ('Content-type', 'text/javascript', true);
            $this->getResponse()->setBody ("/* Wigzo Plugin is Disabled. Service Worker not generated.*/");
        }

        $out = <<<EOL
d=new Date();var cache_key=d.getDate()+"-"+ d.getMonth()+"-"+ d.getFullYear()+"-"+ d.getHours()
var swUrl='https://app.wigzo.com/wigzo_sw.js';importScripts(swUrl+"?orgtoken=$orgId&cache_key="+cache_key);
EOL;

        $this->getResponse()->clearHeaders()->setHeader ('Content-type', 'text/javascript', true);
        $this->getResponse()->setBody ($out);
    }

    private function manifest () {
        $scopeInterface = $this->_objectManager->create ('\Magento\Framework\App\Config\ScopeConfigInterface');

        $enabled = $scopeInterface->getValue ("admin/wigzo/enabled");

        if ($enabled != "true") {
            $this->getResponse()->clearHeaders()->setHeader ('Content-type', 'application/json', true);
            $this->getResponse()->setBody ("{}");
        }

        $orgId = $scopeInterface->getValue ("admin/wigzo/orgId");

        $manifest = array();
        $manifest["name"] = "Wigzo Chrome Push Service";
        $manifest["short_name"] = "Wigzo Push";
        $manifest["display"] = "standalone";
        $manifest["gcm_sender_id"] = "446212695181";
        $manifest["gcm_user_visible_only"] = true;

        $this->getResponse()->clearHeaders()->setHeader ('Content-type', 'application/json', true);
        $this->getResponse()->setBody (json_encode ($manifest));
    }

    public function execute()
    {
        $length = strlen("gcm_manifest");
        if (substr ($_SERVER["REQUEST_URI"], -$length) === "gcm_manifest") {
            $this->manifest ();
        } else {
            $this->serviceWorker ();
        }

    }
}
