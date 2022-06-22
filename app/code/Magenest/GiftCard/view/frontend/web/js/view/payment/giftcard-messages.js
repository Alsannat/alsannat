/**
 * Created by thuy on 19/08/2017.
 */
define([
    'Magento_Ui/js/view/messages',
    '../../model/payment/giftcard-messages'
], function (Component, messageContainer) {
    'use strict';

    return Component.extend({


        initialize: function (config) {
            return this._super(config, messageContainer);
        }
    });
});
