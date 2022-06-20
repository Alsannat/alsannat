define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function (Component, stepNavigator) {
        "use strict";
        return function (abstractTotal) {
            return abstractTotal.extend({
                isFullMode: function() {
                    if (!this.getTotals()) {
                        return false;
                    }
                    return true; //add this line to display forcefully summary in shipping step.
                }
            });
        }
    });