define(
    [
        'ko',
        'jquery',
        'uiElement',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment-service',
        'Amasty_Checkout/js/action/start-place-order',
        'Amasty_Checkout/js/model/amalert',
        'mage/translate',
        'Amasty_Checkout/js/action/focus-first-error',
        'Amasty_Checkout/js/model/payment-validators/login-form-validator',
        'Magento_Ui/js/lib/knockout/extender/bound-nodes',
        'Magento_Ui/js/lib/view/utils/dom-observer',
        'Magento_Ui/js/lib/view/utils/async'
    ],
    function (
        ko,
        $,
        Component,
        registry,
        quote,
        paymentService,
        startPlaceOrderAction,
        alert,
        $t,
        focusFirstError,
        loginFormValidator,
        boundNodes,
        domObserver
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Amasty_Checkout/onepage/place-order',
                defaultLabel: $t('Place Order'),
                visible: true,
                paymentsNamePrefix: 'checkout.steps.billing-step.payment.payments-list.',
                toolbarSelector: '.actions-toolbar',
                placeButtonSelector: '.action.primary',
                originalToolbarPayments: ['braintree_paypal'],
                listens: {
                    'visible': 'onVisibilityChange'
                }
            },

            checkoutRootNode: null,

            previousPaymentMethod: null,

            /**
             * @private
             */
            _asyncCallbackFunction: function () {},

            /**
             * @property {MutationObserver}
             */
            _activePaymentDomObserver: null,

            isPlaceOrderActionAllowed: ko.computed(function () {
                return quote.paymentMethod() && !paymentService.isLoading();
            }),

            initObservable: function () {
                this._super()
                    .observe({label: this.defaultLabel})
                    .observe('visible');

                if (typeof MutationObserver !== 'undefined') {
                    this._activePaymentDomObserver = new MutationObserver(this.mutationCallback.bind(this));
                }

                if (quote.paymentMethod()) {
                    this.paymentMethodSubscriber(quote.paymentMethod());
                }

                quote.paymentMethod.subscribe(this.paymentMethodSubscriber.bind(this));

                return this;
            },

            mutationCallback: function () {
                this.updatePlaceOrderButton(quote.paymentMethod());
            },

            /**
             * When our place button is not visible then original should be
             *
             * @param {Boolean} isVisible
             */
            onVisibilityChange: function (isVisible) {
                this.toggleOriginalToolbar(isVisible);
            },

            /**
             * @param {Boolean} state - is original toolbar (with place order button) should be hided
             */
            toggleOriginalToolbar: function (state) {
               this.getCheckoutRootNode().toggleClass('am-submit-summary', state);
            },

            /**
             * @returns {jQuery}
             */
            getCheckoutRootNode: function () {
                var component,
                    componentNodes;

                if (this.checkoutRootNode === null) {
                    component = registry.get('checkout');
                    componentNodes = boundNodes.get(component);
                    this.checkoutRootNode = $(componentNodes).filter('div');
                }

                return this.checkoutRootNode;
            },

            /**
             * @param {Object|null} paymentMethod
             */
            paymentMethodSubscriber: function (paymentMethod) {
                var paymentToolbar,
                    paymentComponentName;

                if (paymentMethod) {
                    if (this.previousPaymentMethod === paymentMethod.method) {
                        return;
                    }

                    this.previousPaymentMethod = paymentMethod.method;
                }

                this.updatePlaceOrderButton(paymentMethod);

                if (!this._activePaymentDomObserver) {
                    return;
                }

                this._activePaymentDomObserver.disconnect();

                if (!paymentMethod || this.originalToolbarPayments.indexOf(paymentMethod.method) !== -1) {
                    return;
                }

                paymentToolbar = this.getPaymentToolbar(paymentMethod);
                if (paymentToolbar.length) {
                    paymentToolbar.each(function (index, element) {
                        this.registerPaymentObserver(element);
                    }.bind(this));
                } else {
                    paymentComponentName = this.paymentsNamePrefix + paymentMethod.method;

                    domObserver.off(this.toolbarSelector, this._asyncCallbackFunction);

                    this._asyncCallbackFunction = function (element) {
                        var component = registry.get(paymentComponentName);

                        this._activePaymentDomObserver.disconnect();
                        this.updatePlaceOrderButton(paymentMethod);
                        this.registerPaymentObserver(element);
                        domObserver.off(this.toolbarSelector, this._asyncCallbackFunction);
                        boundNodes.off(component);
                    }.bind(this);

                    $.async({
                        component: paymentComponentName,
                        selector: this.toolbarSelector
                    }, this._asyncCallbackFunction);
                }
            },

            /**
             * observe all active toolbars and update button label (or change visibility) on change
             *
             * @param {HTMLElement} element
             */
            registerPaymentObserver: function (element) {
                var button = $(element).find(this.placeButtonSelector).get(0);

                this._activePaymentDomObserver.observe(
                    element,
                    {
                        attributes: true,
                        attributeFilter: ['style', 'class'],
                        characterData: true
                    }
                );

                if (button) {
                    //observe button text
                    this._activePaymentDomObserver.observe(
                        button,
                        {
                            subtree: true,
                            characterData: true
                        }
                    );
                }
            },

            /**
             * @param {Object|null} paymentMethod
             */
            updatePlaceOrderButton: function (paymentMethod) {
                var paymentToolbar,
                    button;

                if (!paymentMethod) {
                    this.visible(true);

                    return;
                }

                paymentToolbar = this.getPaymentToolbar(paymentMethod);

                if (paymentToolbar.length === 0 || this.originalToolbarPayments.indexOf(paymentMethod.method) !== -1) {
                    this.visible(false);

                    return;
                }

                if (paymentToolbar.length > 1) {
                    //selector by attribute style should be used instread of :visible,
                    //because some paypal payments can render 2 buttons and thay are both hidden by our css
                    //but not active is hidden by js with attribute style
                    paymentToolbar = paymentToolbar.filter(':not([style*="display: none"])');
                }

                button = paymentToolbar.find(this.placeButtonSelector);

                if (button.length) {
                    this.visible(true);
                    this.updateLabel(button);
                } else {
                    this.visible(false);
                }
            },

            /**
             * Selected payment isn't have class `_active` yet
             *
             * @param {Object} paymentMethod
             *
             * @returns {jQuery}
             */
            getPaymentToolbar: function (paymentMethod) {
                return $('#' + paymentMethod.method).parents('.payment-method')
                    .find(this.toolbarSelector);
            },

            /**
             * @param {JQuery|Element} button
             */
            updateLabel: function (button) {
                var buttonText = button.text();

                if (buttonText && buttonText.trim() !== "") {
                    this.label(buttonText);

                    return;
                }

                if (button.attr('title')) {
                    this.label(button.attr('title'));

                    return;
                }

                this.label(this.defaultLabel);
            },

            placeOrder: function () {
                var errorMessage = '';

                if (!quote.paymentMethod()) {
                    errorMessage = $.mage.__('No payment method selected');
                    alert({content: errorMessage});
                    return;
                }

                if (!quote.shippingMethod() && !quote.isVirtual()) {
                    errorMessage = $.mage.__('No shipping method selected');
                    alert({content: errorMessage});
                    return;
                }

                var validateBillingAddress = this.updateBillingAddress(quote);
                var validateShippingAddress = this.updateShippingAddress(quote);

                if (loginFormValidator.validate() && !validateBillingAddress && !validateShippingAddress) {
                    startPlaceOrderAction();
                } else {
                    focusFirstError();
                }
            },

            updateBillingAddress: function (quote) {
                var billingAddress = null;

                if (window.checkoutConfig.displayBillingOnPaymentMethod) {
                    billingAddress =
                        registry.get('checkout.steps.billing-step.payment.payments-list.'
                            + quote.paymentMethod().method
                            + '-form');
                } else {
                    billingAddress =
                        registry.get("checkout.steps.billing-step.payment.afterMethods.billing-address-form");

                    if (!billingAddress) {
                        billingAddress =
                            registry.get("checkout.steps.shipping-step.shippingAddress.billing-address-form");
                    }
                }

                if (!billingAddress || billingAddress.isAddressSameAsShipping() || billingAddress.isAddressDetailsVisible()) {
                    return false;
                } else {
                    billingAddress.updateAddress();

                    return billingAddress.source.get('params.invalid');
                }
            },

            updateShippingAddress: function (quote) {
                var shippingAddress = registry.get("checkout.steps.shipping-step.shippingAddress.address-list");

                if (shippingAddress && typeof shippingAddress.updateAddress !== "undefined") {
                    shippingAddress.updateAddress();

                    return shippingAddress.source.get('params.invalid');
                }

                return false;
            },
        });
    }
);