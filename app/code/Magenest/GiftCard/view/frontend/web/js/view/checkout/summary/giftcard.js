/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Magento_Catalog/js/price-utils'
    ],
    function (Component, quote, totals) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_GiftCard/checkout/summary/giftcard'
            },
            isIncludedInSubtotal: window.checkoutConfig.isIncludedInSubtotal,
            totals: totals.totals,

            /**
             * @returns {Number}
             */
            getGiftCadSegment: function () {
                var giftcard = totals.getSegment('giftcard') || totals.getSegment('giftcard_code');

                if (giftcard !== null && giftcard.hasOwnProperty('value')) {
                    return giftcard.value;
                }

                return 0;
            },

            /**
             * Get giftcard value
             * @returns {String}
             */
            getValue: function () {
                return this.getFormattedPrice(this.getGiftCadSegment());
            },

            /**
             * giftcard display flag
             * @returns {Boolean}
             */
            isDisplayed: function () {
                return this.isFullMode() && this.getGiftCadSegment() > 0;
            }
        });
    }
);
