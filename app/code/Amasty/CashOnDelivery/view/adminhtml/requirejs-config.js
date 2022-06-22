/*jshint browser:true jquery:true*/
/*global alert*/
var config = {
    config: {
        mixins: {
            'Magento_Sales/order/create/scripts': {
                'Amasty_CashOnDelivery/js/order/create/scripts-mixin': !window.amasty_cash_on_delivery_disabled
            },
        }
    }
};
