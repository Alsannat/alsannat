///var/www/html/woomodule/woocommerceticket/wp-content/plugins/woocommerce-event-ticket/js/pdfchain.js
define([
    "jquery",
    "uiClass",
    "mage/validation",
    'Magento_Ui/js/lib/spinner',
    "Magenest_GiftCard/js/product/giftcardPrice",
    "underscore"
], function ($, Class,validation,loader, PriceScheme, _) {
    "use strict";
    return Class.extend({
        defaults: {
            theSelector :"select[name='product\\[giftcard_price_scheme\\]']",
            fixedPriceSelector:'input[name="product\\[gc_fixed_price\\]"]',
            dropdownPriceSelector: 'input[name="product\\[giftcard_price_selector\\]"]',
            minPriceSelector:'input[name="product\\[gc_min_price\\]"]',
            maxPriceSelector : 'input[name="product\\[gc_max_price\\]"]'
        },
        /**
         * Constructor
         */
        initialize: function (config) {

            this.initConfig(config);

            this.initState();
            this.attachAction(config);

            return this;
        },
        /**
         * get initial value of the selector then show the appropriate the
         * price fields
         */
        initState : function () {
            var self = this;
            var theElementSelector =self.theSelector;

            var priceSchemeElement = $(theElementSelector);

            var schemeValue = parseInt(priceSchemeElement.val());

            self.hideDefaultPriceElement();
            self.hideAllPriceElement();
            self.showProperPriceField(schemeValue);
        },

        /**
         * Hide the default price to avoid making admin get confused
         */
        hideDefaultPriceElement: function() {
            var defaultPriceElement = $('input[name="product\\[price\\]"]');

            var price = parseFloat(defaultPriceElement.val());
            if ( price > 0) {
                defaultPriceElement.parent().parent().parent().hide();
              } else {
                defaultPriceElement.val(1).parent().parent().parent().hide().trigger('keyup');
               }

        },
        /**
         *
         * @param config
         */
        attachAction: function (config) {
            try {
            var self = this;

            var theElementSelector =self.theSelector;

            var priceSchemeElement = $(theElementSelector);



            $(self.theSelector).on('change', function() {
               // alert('change the price scheme');
                var thePriceScheme = parseInt($(self.theSelector).val());

                self.hideAllPriceElement();
                self.showProperPriceField(thePriceScheme);
            });

            var preventSubmit = function(preventSubmit) {
                event.preventDefault();
                return false;
            }

            $('#save-button').on('click',function() {

                $('#product_form').unbind('submit', preventSubmit);
                var giftCardElement = $('.giftcard-price-active').first();
                var giftCardValue   = $('.giftcard-price-active').first().val();

                // var isValidate = $.validator.methods.required(giftCardValue , giftCardElement );

                var isValidate =  /^\d+$/.test(giftCardValue);
                if (!isValidate) {

                    //jQuery('#product_form');
                    $('#product_form').bind('submit',function() {
                        return false;
                    });

                }

            });
            } catch (e) {
                console.log(e);
            }
        },
        /**
         *
         * @param $scheme
         */
        showProperPriceField:function($scheme) {
            //console.log('show properly price');
            switch($scheme) {
                case 0:
                    $(this.fixedPriceSelector).addClass('giftcard-price-active');
                    $(this.fixedPriceSelector).parent().parent().show();
                    break;
                case 1:
                    $(this.dropdownPriceSelector).parent().parent().show();
                    $(this.dropdownPriceSelector).addClass('giftcard-price-active');

                    break;
                case 2:
                    $(this.minPriceSelector).parent().parent().show();
                    $(this.minPriceSelector).addClass('giftcard-price-active');

                    // $(this.minPriceSelector).rules("add" , { required: true});

                    $(this.maxPriceSelector).parent().parent().show();
                    $(this.maxPriceSelector).addClass('giftcard-price-active');

                    break;
                default:
                    $(this.fixedPriceSelector).parent().parent().show();
                    $(this.fixedPriceSelector).parent().parent().show();


            }
            $(".giftcard-price-active").each(function(item) {
               // if ($(item).form != undefined) {
                    //$(item).rules("add", {
                    //    required: true,
                    //    messages: {
                    //        required: "This is required field"
                    //    }
                    //});
               // }

            });
        },
        /**
         *
         */
        hideAllPriceElement : function() {
            var self = this;
            var priceTypeSelector = [self.fixedPriceSelector, self.dropdownPriceSelector,self.minPriceSelector,self.maxPriceSelector];

            for (var i =0; i < priceTypeSelector.length; i++) {
                var parenSelector = $(priceTypeSelector[i]).parent().parent();
                parenSelector.hide();

            }

            //remove class giftcard-price-active and remove the rule too
            $('.giftcard-price-active').each(function(item) {
                $(item).removeClass("giftcard-price-active");

               // $(item).rules('remove');

            });



        }
    });
});

