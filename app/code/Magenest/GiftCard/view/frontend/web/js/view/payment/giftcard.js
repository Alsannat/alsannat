define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'mage/storage',
        'mage/loader',
        'mage/translate',
        'Magento_Checkout/js/model/resource-url-manager',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_SalesRule/js/model/payment/discount-messages'
    ],
    function ($,
              ko,
              Component,
              quote,
              storage,
              loader,
              $t,
              urlManager,
              getPaymentInformationAction,
              totals,
              fullScreenLoader,
              messageContainer
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magenest_GiftCard/checkout/payment/apply'
            },
            isLoading: true,
            hasGiftCard: false,
            giftcardCode: '',
            loadingIcon: 'loader-2.gif',
            feedUrl :'/giftcard/cart/load',
            cancelUrl : '/giftcard/cart/remove',
            submitUrl : '/giftcard/cart/add',
           // error : messageContainer,
            initObservable: function () {
                this._super()
                    .observe({
                        isLoading:true,
                        hasGiftCard:false,
                        giftcardCode:''

                    });
                var self = this;

                var options ;
               self.isLoading.subscribe(function(newValue) {
                    if (newValue) {
                        $('div[data-role="loader"]').show();
                    } else {
                        $('div[data-role="loader"]').hide();
                    }

                },self);

                var quoteFeedUrl =  MY_BASE_URL +  this.feedUrl;
                self.loadingIcon = window.giftCardLoaderIcon;

                $.ajax({
                    url: quoteFeedUrl,
                    async :false,
                    global: true,
                    contentType : 'application/json',
                }).done(function(response){
                    var responseData = response;
                    if (responseData.giftcard != undefined) {
                    self.giftcardCode(responseData.giftcard);

                    if (responseData.giftcard.length > 0) {
                        self.hasGiftCard(true);
                    }
                    }
                    self.isLoading(false);

                });

                return this;
            },

            /**
             * apply gift card code to the quote
             */
            apply: function() {
                var self = this;
                self.isLoading(true);
               // $('div[data-role="loader"]').show();
                var form = $('form[data-action="giftcard-form"]');
                //fullScreenLoader.startLoader();

                return  $.ajax({
                        url: MY_BASE_URL+ self.submitUrl,
                        type: 'post',
                        async: false,
                        data: form.serialize()
                    }
                ).done(
                    function (response) {
                      //  $('div[data-role="loader"]').hide();

                        if (response) {

                            var deferred = $.Deferred();
                            var responseCode = response.code;
                            var responseMessage = response.message;

                            if (responseCode == 'ok' ) {

                                self.isLoading(false);
                                self.hasGiftCard(true);
                                self.giftcardCode($('#giftcard_code').val());
                                totals.isLoading(true);
                                getPaymentInformationAction(deferred);
                                $.when(deferred).done(function () {
                                    totals.isLoading(false);
                                });
                               // fullScreenLoader.stopLoader();

                                self.regions.messages()[0].messageContainer.addSuccessMessage({'message': responseMessage});

                            }  else if (responseCode == 'error') {
                                self.isLoading(false);
                                //fullScreenLoader.stopLoader();

                                self.regions.messages()[0].messageContainer.addErrorMessage({'message': responseMessage});

                            }

                        }
                    }
                ).fail(
                    function (response) {
                        self.isLoading(false);
                       // fullScreenLoader.stopLoader();
                    }
                ).always(
                    function () {
                        self.isLoading(false);
                    }
                );;


            },

            /**
             * cancel gift card
             */
            cancel: function() {
                var self = this;
                self.isLoading(true);
                var form = $('form[data-action="giftcard-form"]');
                //fullScreenLoader.startLoader();
                //                    MY_BASE_URL + self.cancelUrl,

                return $.ajax(
                    {
                        url :  MY_BASE_URL + self.cancelUrl,
                        type: 'post',
                        async: false,
                        data: form.serialize()
                    }
                ).done(
                    function (response) {

                        var deferred = $.Deferred();
                        var responseCode = response.code;
                        var responseMessage = response.message;
                        if (responseCode == 'ok' ) {
                            totals.isLoading(true);
                            getPaymentInformationAction(deferred);
                           // fullScreenLoader.stopLoader();

                            $.when(deferred).done(function () {
                                self.hasGiftCard(false);
                                self.giftcardCode('');
                                totals.isLoading(false);

                                self.regions.messages()[0].messageContainer.addSuccessMessage({'message': responseMessage});

                            });
                        } else {
                            self.isLoading(false);
                            fullScreenLoader.stopLoader();

                            self.regions.messages()[0].messageContainer.addErrorMessage({'message': responseMessage});
                        }
                    }
                ).fail(
                    function (response) {
                        //fullScreenLoader.stopLoader();
                        totals.isLoading(false);
                    }
                ).always(
                    function () {
                        self.isLoading(false);
                    }
                );

            }
        });
    }
);
