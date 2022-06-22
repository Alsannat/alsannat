define([
    'underscore',
    'mage/utils/wrapper',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-payment-method'
],function (_, wrapper, checkoutData, paymentService, selectPaymentMethodAction) {
    'use strict';

    return function (checkoutDataResolver) {
        var check = window.checkoutConfig.payment['checkoutcom_magento2'];
        var ckoConfig = window.checkoutConfig.payment['checkoutcom_magento2'].checkoutcom_configuration

        /**
         * Auto select the last used payment method. If this is unavailable select the default.
         */
        var resolvePaymentMethod = wrapper.wrap(
            checkoutDataResolver.resolvePaymentMethod,
            function (originalResolvePaymentMethod) {
                var availablePaymentMethods = paymentService.getAvailablePaymentMethods();
                var method = this.getMethod(checkoutData.getSelectedPaymentMethod(), availablePaymentMethods);
                
                if ((!checkoutData.getSelectedPaymentMethod() && _.size(availablePaymentMethods) > 1) || _.isUndefined(method)) {
                    var method = this.getMethod(check['checkoutcom_data']['user']['previous_method'], availablePaymentMethods);
                    
                    if (!_.isUndefined(method)) {
                        selectPaymentMethodAction(method);
                    } else {
                        var method = this.getMethod(ckoConfig.default_method, availablePaymentMethods);
                        var isiDevice = /ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase());
                        if (!_.isUndefined(method)) {
                            if(isiDevice){
                                var applePayVar = setInterval(function(){ applePayFun() }, 500);
                                function applePayFun() {
                                    if (jQuery('#checkoutcom_apple_pay').length){
                                        var applePayment = {title: "اآبل باي", method: "checkoutcom_apple_pay"};
                                        selectPaymentMethodAction(applePayment);
                                        clearInterval(applePayVar);
                                    }
                                }
                            }else{
                                selectPaymentMethodAction(method);
                            }
                        }
                    }
                }
                return originalResolvePaymentMethod();
            }
        )

        return _.extend(checkoutDataResolver, {
            resolvePaymentMethod: resolvePaymentMethod,

            /**
             * Get the payment method
             *
             * @param  {Array} availableMethods
             * @return {Object|undefined}
             */
            getMethod: function (method, availableMethods) {
                var autoselectMethod = method
                var matchedMethod;
                if (!_.isUndefined(autoselectMethod)) {
                    matchedMethod = availableMethods.find(function (method) {
                        return method.method === autoselectMethod;
                    });
                }
                
                return matchedMethod;
            }
        });
    };
});