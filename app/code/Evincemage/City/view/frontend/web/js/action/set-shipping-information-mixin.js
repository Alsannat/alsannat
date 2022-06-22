define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote, shippingFields) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, container) {

            var shippingAddress = quote.shippingAddress(),
                shippingCity = $("#shipping-new-address-form [name = 'city'] option:selected"),
                shippingCityValue = shippingCity.val();

            var shippingDistrictId = $("#shipping-new-address-form [name='district'] option:selected"),
                shippingDistrictIdValue = shippingDistrictId.val(); 

            var shippingDistrictText = $("#shipping-new-address-form [name='district_text']").val();
            shippingAddress.city = shippingCityValue;
            if(shippingDistrictText==""||shippingDistrictText==undefined)
            {

                shippingAddress.region = shippingDistrictIdValue;
            }
            else
            {
                shippingAddress.region = shippingDistrictText;
            }
            console.log("before original action");
            return originalAction(container);
        });
    };
});
