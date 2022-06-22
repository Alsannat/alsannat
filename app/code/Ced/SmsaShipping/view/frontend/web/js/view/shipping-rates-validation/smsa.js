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
        'Ced_Smsa/js/model/shipping-rates-validator/smsa',
        'Ced_Smsa/js/model/shipping-rates-validation-rules/smsa'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        smsaShippingRatesValidator,
        smsaShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('smsa', smsaShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('smsa', smsaShippingRatesValidationRules);
        return Component;
    }
);
