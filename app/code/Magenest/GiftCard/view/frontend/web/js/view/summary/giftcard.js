/**
 * Created by root on 01/01/2017.
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

        return Component.extend(
            {
                defaults: {
                    template: 'Magenest_GiftCard/checkout/summary/giftcard'
                },
                isIncludedInSubtotal: window.checkoutConfig.isIncludedInSubtotal,
                totals: totals.totals,

                /**
                 * @returns {Number}
                 */
                getGiftCardSegment: function () {
                    var giftcard = totals.getSegment('giftcard') || totals.getSegment('giftcard');

                    if (giftcard !== null && giftcard.hasOwnProperty('value')) {
                        return giftcard.value;
                    }

                    return 0;
                },

                /**
                 * Get weee value
                 *
                 * @returns {String}
                 */
                getValue: function () {
                    return this.getFormattedPrice(this.getGiftCardSegment());
                },

                /**
                 * Gift Card display flag
                 *
                 * @returns {Boolean}
                 */
                isDisplayed: function () {
                    return this.isFullMode() && this.getGiftCardSegment() > 0;
                }
            }
        );
    }
);