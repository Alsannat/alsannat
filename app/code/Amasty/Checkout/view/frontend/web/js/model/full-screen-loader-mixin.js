define(
    [
        'mage/utils/wrapper',
        'Magento_Checkout/js/model/payment-service'
    ],
    function (wrapper, paymentService) {
        'use strict';

        return function (target) {
            /**
             * Override for avoid full screen on shipping dynamic save
             */
            target.startLoader =  wrapper.wrapSuper(target.startLoader, function () {
                if (window.loaderIsNotAllowed) {
                    paymentService.isLoading(true);
                } else {
                    this._super();
                }
            });

            return target;
        };
    }
);

