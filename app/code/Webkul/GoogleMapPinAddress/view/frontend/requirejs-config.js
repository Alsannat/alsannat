/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

var config = {
    map: {
        '*': {
            mapjs: 'Webkul_GoogleMapPinAddress/js/mapJs'
        }
    },
    config: {
        mixins: {
        'Magento_Checkout/js/action/set-billing-address': {
            'Webkul_GoogleMapPinAddress/js/action/set-billing-address-mixin': true
        },
        'Magento_Checkout/js/action/set-shipping-information': {
            'Webkul_GoogleMapPinAddress/js/action/set-shipping-information-mixin': true
        },
        'Magento_Checkout/js/action/create-shipping-address': {
            'Webkul_GoogleMapPinAddress/js/action/create-shipping-address-mixin': true
        },
        'Magento_Checkout/js/action/place-order': {
            'Webkul_GoogleMapPinAddress/js/action/set-billing-address-mixin': true
        },
        'Magento_Checkout/js/action/create-billing-address': {
            'Webkul_GoogleMapPinAddress/js/action/set-billing-address-mixin': true
        }
    }
}
};