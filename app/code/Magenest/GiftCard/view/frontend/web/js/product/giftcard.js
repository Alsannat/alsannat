
define([
    "jquery",
    "uiClass",
    'Magento_Ui/js/lib/spinner',
    "Magenest_GiftCard/js/product/giftcard",
    "ko",
    "underscore"
], function ($, Class,loader, giftcard,ko, _) {
    "use strict";
    return Class.extend({
        giftcardTemplate : ko.observable(),
        updateGiftCardTemplate:function (data, event) {
            console.log(data);  console.log(event);

        },
        defaults: {
            buttonRefresh: '.button.action-refresh'
        },
        /**
         * Constructor
         */
        initialize: function (config) {

            this.initConfig(config);


            return this;
        }

    })
});
