define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/shipping-rates-validator/naqelshipping',
    '../../model/shipping-rates-validation-rules/naqelshipping'
], function (Component,
             defaultShippingRatesValidator,
             defaultShippingRatesValidationRules,
             customShippingRatesValidator,
             customShippingRatesValidationRules) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('naqelshipping', customShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('naqelshipping', customShippingRatesValidationRules);

    return Component;
});