define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Amasty_CashOnDelivery/js/model/cart/totals-processor/default',
    'Magento_Checkout/js/model/payment/method-list'
], function (ko, $, Component, quote, recollect, methodList) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._insertPolyfills();
            this._super();

            if (!window.amasty_cash_on_delivery_disabled) {
                quote.paymentMethod.subscribe(recollect);
                methodList.subscribe(recollect);
            }

            return this;
        },

        _insertPolyfills: function () {
            if (typeof Object.assign != 'function') {
                // Must be writable: true, enumerable: false, configurable: true
                Object.defineProperty(Object, "assign", {
                    value: function assign(target, varArgs) { // .length of function is 2
                        'use strict';
                        if (target == null) { // TypeError if undefined or null
                            throw new TypeError('Cannot convert undefined or null to object');
                        }

                        var to = Object(target);

                        for (var index = 1; index < arguments.length; index++) {
                            var nextSource = arguments[index];

                            if (nextSource != null) { // Skip over if undefined or null
                                for (var nextKey in nextSource) {
                                    // Avoid bugs when hasOwnProperty is shadowed
                                    if (Object.prototype.hasOwnProperty.call(nextSource, nextKey)) {
                                        to[nextKey] = nextSource[nextKey];
                                    }
                                }
                            }
                        }
                        return to;
                    },
                    writable: true,
                    configurable: true
                });
            }
        }
    });
});
