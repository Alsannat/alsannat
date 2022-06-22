var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Custom_AddOrderSumaryStepOne/js/view/summary/abstract-total-mixin': true
            },
            'Magento_Checkout/js/view/summary/shipping': {
                'Custom_AddOrderSumaryStepOne/js/view/summary/shipping-mixin': true
            },
        }
    }};