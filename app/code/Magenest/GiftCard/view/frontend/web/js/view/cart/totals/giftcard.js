/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'Magenest_GiftCard/js/view/checkout/summary/giftcard'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isFullMode: function () {
                return true;
            }
        });
    }
);
