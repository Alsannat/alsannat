define(
    [
        'ko',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils'
    ],
    function (ko, Component, quote, priceUtils) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Aheadworks_OneStepCheckout/totals/discount'
            },
            totals: quote.getTotals(),

            /**
             * Check if total displayed
             *
             * @returns {boolean}
             */
            isDisplayed: function() {
                return this.getPureValue() != 0;
            },

            /**
             * Get coupon code
             *
             * @returns {string|null}
             */
            getCouponCode: function() {
                if (!this.totals()) {
                    return null;
                }
                return this.totals()['coupon_code'];
            },

            /**
             * Get pure total value
             *
             * @returns {Number}
             */
            getPureValue: function() {
                return this.totals() && this.totals().discount_amount
                    ? parseFloat(this.totals().discount_amount)
                    : 0;
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
             * Get formatted total value
             *
             * @returns {string}
             */
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
