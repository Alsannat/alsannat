<?php

namespace Amasty\Acart\Model\App\Response;

class Redirect extends \Magento\Store\App\Response\Redirect
{
    /**
     * @param string $url
     *
     * @return string
     */
    public function validateRedirectUrl($url)
    {
        if (!$this->_isUrlInternal($url)) {
            $url = $this->_storeManager->getStore()->getBaseUrl();
        } else {
            $url = $this->normalizeRefererUrl($url);
        }

        return $url;
    }
}
