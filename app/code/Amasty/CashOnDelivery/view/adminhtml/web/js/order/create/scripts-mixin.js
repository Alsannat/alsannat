define(
    [
    'underscore'
    ],
    function (_) {
        'use strict';

        return function () {
            var originalLoadArea = window.AdminOrder.prototype.loadArea,
                originalChangeAddressField = window.AdminOrder.prototype.changeAddressField,
                originalSetShippingMethod = window.AdminOrder.prototype.setShippingMethod,
                originalSwitchPaymentMethod = window.AdminOrder.prototype.switchPaymentMethod,
                reloadPayment = false,
                reloadTotals = false;

            window.AdminOrder.prototype.setShippingMethod = function (method) {
                reloadPayment = true;
                reloadTotals = true;
                originalSetShippingMethod.apply(this, [method]);
                reloadPayment = false;
                reloadTotals = false;
            };

            window.AdminOrder.prototype.switchPaymentMethod = function (method) {
                reloadTotals = true;
                originalSwitchPaymentMethod.apply(this, [method]);
                reloadTotals = false;
            };

            window.AdminOrder.prototype.changeAddressField = function (event) {
                reloadPayment = true;
                reloadTotals = true;
                originalChangeAddressField.apply(this, [event]);
                reloadPayment = false;
                reloadTotals = false;
            };

            window.AdminOrder.prototype.loadArea = function (area, indicator, params) {
                var areaCustom = area ? area : [];

                if (Array.isArray(areaCustom)) {
                    if (reloadPayment) {
                        areaCustom.push('billing_method');
                    }

                    if (reloadTotals) {
                        areaCustom.push('totals');
                    }

                    if (reloadTotals || reloadPayment) {
                        indicator = true;
                    }

                    areaCustom = _.unique(areaCustom);
                }

                originalLoadArea.apply(this, [areaCustom, indicator, params]);
            }
        };
    }
);
