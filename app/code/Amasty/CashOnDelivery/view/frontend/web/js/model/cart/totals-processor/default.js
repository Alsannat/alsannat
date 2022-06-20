define([
    'jquery',
    'mage/utils/wrapper',
    'underscore',
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/error-processor'
], function ($, wrapper, _, resourceUrlManager, quote, storage, totalsService, errorProcessor) {
    'use strict';
    var ajax;

    return function (method) {
        var serviceUrl,
            payload,
            address,
            paymentMethod = quote.paymentMethod(),
            requiredFields = ['countryId', 'region', 'regionId', 'postcode'];

        if (!paymentMethod) {
            return;
        }

        if (ajax) {
            ajax.abort();
        }

        totalsService.isLoading(true);

        serviceUrl = resourceUrlManager.getUrlForTotalsEstimationForNewAddress(quote);
        address = _.pick(quote.shippingAddress(), requiredFields);

        address.extension_attributes = {
            payment_method: paymentMethod.method
        };

        payload = {
            addressInformation: {
                address: address
            }
        };

        if (quote.shippingMethod() && quote.shippingMethod()['method_code']) {
            payload.addressInformation['shipping_method_code'] = quote.shippingMethod()['method_code'];
            payload.addressInformation['shipping_carrier_code'] = quote.shippingMethod()['carrier_code'];
        }

        ajax = storage.post(
            serviceUrl,
            JSON.stringify(payload),
            false
        ).done(function (result) {
            quote.setTotals(result);
            // Stop loader for totals block
            totalsService.isLoading(false);
        }).fail(function (response) {
            if (response.responseText || response.status) {
                errorProcessor.process(response);
            }
        });
    };
});
