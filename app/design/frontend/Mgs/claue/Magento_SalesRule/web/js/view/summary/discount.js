/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils'
], function (Component, quote, priceUtils) {
    'use strict';

    return Component.extend({ 
        defaults: {
            template: 'Magento_SalesRule/summary/discount'
        },
        totals: quote.getTotals(),

        /**
         * @return {*|Boolean}
         */
        isDisplayed: function () {
            return this.isFullMode() && this.getPureValue() != 0; //eslint-disable-line eqeqeq
        },

        /**
         * @return {*}
         */
        getCouponCode: function () {
            if (!this.totals()) {
                return null;
            }
            var coupencode = "("+this.totals()['coupon_code']+")";
            return coupencode;
        },

        /**
         * @return {*}
         */
        getCouponLabel: function () {
            if (!this.totals()) {
                return null;
            }

            return this.totals()['coupon_label'];
        },

        /**
         * Get discount title
         *
         * @returns {null|String}
         */
        getTitle: function () {
           // var discountSegments;

            if (!this.totals()) {
                return null;
            }

            // discountSegments = this.totals()['total_segments'].filter(function (segment) {
            //     return segment.code.indexOf('discount') !== -1;
            // });

            //return discountSegments.length ? discountSegments[0].title : null;
            var discount = 'Discount';
            return discount;
        },

        /**
         * @return {Number}
         */
        getPureValue: function () {
            var price = 0;

            if (this.totals() && this.totals()['discount_amount']) {
                price = parseFloat(this.totals()['discount_amount']);
            }

            return price;
        },

        getFormattedPrice: function(price) {
            if (price % 1 == 0) {
                quote.getPriceFormat()["precision"] = 0;
                quote.getPriceFormat()["requiredPrecision"] = 0;
            } else {
                quote.getPriceFormat()["precision"] = 2;
                quote.getPriceFormat()["requiredPrecision"] = 2;
            }

            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * @return {*|String}
         */
        getValue: function () {
            return this.getFormattedPrice(this.getPureValue());
        }
    });
});
