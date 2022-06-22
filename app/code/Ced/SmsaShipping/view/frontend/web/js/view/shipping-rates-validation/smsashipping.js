/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Ced_SmsaShipping/js/model/shipping-rates-validator/smsashipping',
        'Ced_SmsaShipping/js/model/shipping-rates-validation-rules/smsashipping'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        smsaShippingRatesValidator,
        smsaShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('smsashipping', smsaShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('smsashipping', smsaShippingRatesValidationRules);
        return Component;
    }
);
