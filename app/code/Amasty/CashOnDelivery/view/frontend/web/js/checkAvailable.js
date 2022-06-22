define([
    'jquery',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage',
    'jquery-ui-modules/widget'
], function ($, urlBuilder, storage) {
    'use strict';

    $.widget('mage.amCodCheckAvailable', {
        options: {
            successMessage: $("[data-amcashdel-js='allowed-postal-code']"),
            errorMessage: $("[data-amcashdel-js='not-allowed-postal-code']"),
            postalCode: $('#postal_code'),
            submit: $("[data-amcashdel-js='amcashdel-check-status']")
        },

        _create: function () {
            this._super();
            this.options.submit.on('click', function () {
                var postalCode = this.options.postalCode.val();

                if (postalCode) {
                    storage.post(
                        urlBuilder.createUrl('/cash_on_delivery/checkAvailable/:postalCode', {postalCode: postalCode}),
                        JSON.stringify({}),
                        false
                    ).done(function (result) {
                        if (result) {
                            this.options.successMessage.show();
                            this.options.errorMessage.hide();
                        } else {
                            this.options.errorMessage.show();
                            this.options.successMessage.hide();
                        }
                    }.bind(this));
                }
            }.bind(this));
        }
    });

    return $.mage.amCodCheckAvailable;
});
