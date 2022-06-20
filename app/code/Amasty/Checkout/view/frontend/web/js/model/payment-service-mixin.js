define([
    'ko',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Amasty_Checkout/js/model/vault-payment-resolver'
],function (ko, wrapper, quote, vaultResolver) {
    'use strict';
    return function (target) {
        /**
         * add loader block for payment
         */
        target.isLoading = ko.observable(false);

        /**
         * Fix unselection of saved vault payment method
         */
        target.setPaymentMethods = wrapper.wrapSuper(target.setPaymentMethods, function (methods) {
            if (methods && quote.paymentMethod()) {
                var selectedMethod = quote.paymentMethod().method;
                if (vaultResolver.isVaultMethodAvailable(selectedMethod, methods)) {
                    methods.push({
                        method: selectedMethod
                    });
                }
            }

            this._super(methods);
            this.isLoading(false);
        });

        return target;
    };
});
