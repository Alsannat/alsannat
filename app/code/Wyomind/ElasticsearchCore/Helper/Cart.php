<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Frontend Cart Helper
 */
class Cart extends \Magento\Checkout\Helper\Cart
{
    /**
     * Get the UENC code
     * @return string
     */
    public function getUenc()
    {
        $uenc = $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl());
        return $uenc;
    }

    /**
     * Retrieve the "add to cart" url
     * @param array $additional
     * @return string
     */
    public function getAddUrlPlaceHolder($additional = [])
    {
        if (isset($additional['useUencPlaceholder'])) {
            $uenc = "%uenc%";
            unset($additional['useUencPlaceholder']);
        } else {
            $uenc = $this->urlEncoder->encode($this->_urlBuilder->getCurrentUrl());
        }

        $urlParamName = \Magento\Framework\App\ActionInterface::PARAM_NAME_URL_ENCODED;

        $routeParams = [
            $urlParamName => $uenc,
            'product' => 'productId',
            '_secure' => $this->_getRequest()->isSecure()
        ];

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($this->_getRequest()->getRouteName() == 'checkout' && $this->_getRequest()->getControllerName() == 'cart'
        ) {
            $routeParams['in_cart'] = 1;
        }

        return $this->_getUrl('checkout/cart/add', $routeParams);
    }
}