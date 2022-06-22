ReadeMeMFTF (It is recommendations for runs tests to module One Step Checkout).

    Quantity all tests equals 24.

        For runs all tests, use group "OSC".
            command: "vendor/bin/mftf run:group OSC -r"

        For runs tests checking module configuration, use group "OSCConfiguration".
                    command: "vendor/bin/mftf run:group OSCConfiguration -r"

        For runs tests checking only main functions module, use group "OSCFunctional".
                            command: "vendor/bin/mftf run:group OSCFunctional -r"  

        Some tests (qty 18) check Payments Methods with credentials (Amazon, Authorise, Braintree, Braintree PayPal,eWay,
         Klarna, Payflow Pro, PayPal, Stripe). For correct operation you have to fill your credentials in
         file "PaymentMethodsCredentialsData" (directory "Data" in module "CommonTests"), and fill your test credit
         card data in file "CreditCardsData" (directory "Data" in module "Checkout").

        full path to "PaymentMethodsCredentialsData" : "/magento/vendor/amasty/module-common-tests/Test/Mftf/Data/PaymentMethodsCredentialsData"
                                                       or "/magento/app/code/Amasty/CommonTests/Test/Mftf/Data/PaymentMethodsCredentialsData"
        full path to "CreditCardsData" : "/magento/vendor/amasty/module-single-step-checkout/Test/Mftf/Data/CreditCardsData"
                                         or "/magento/app/code/Amasty/Checkout/Test/Mftf/Data/CreditCardsData"



        For runs tests checking all Payment Methods (Amazon, Authorise, Braintree, Braintree PayPal, eWay, Klarna,
            Payflow Pro, PayPal, Stripe, Bank Transfer, Cash On Delivery, Purchase Order), use group "PaymentMethodsOnOSC".
            command: "vendor/bin/mftf run:group PaymentMethodsOnOSC -r"

        Check only one of next payment methods:
            For checking only "Amazon", use command: "vendor/bin/mftf run:group PaymentAmazon -r"
            For checking only "Authorise", use command: "vendor/bin/mftf run:group PaymentAuthorise -r"
            For checking only "Bank Transfer", use command: "vendor/bin/mftf run:group PaymentBankTransfer -r"
            For checking only "Braintree", use command: "vendor/bin/mftf run:group PaymentBraintree -r"
            For checking only "Cash On Delivery", use command: "vendor/bin/mftf run:group PaymentCashOnDelivery -r"
            For checking only "eWay", use command: "vendor/bin/mftf run:group PaymentEWay -r"
            For checking only "Klarna", use command: "vendor/bin/mftf run:group PaymentKlarna -r"
            For checking only "Payflow Pro", use command: "vendor/bin/mftf run:group PaymentPayflowPro -r"
            For checking only "PayPal", use command: "vendor/bin/mftf run:group PaymentPayPal -r"
            For checking only "Purchase Order", use command: "vendor/bin/mftf run:group PaymentPurchaseOrder -r"
            For checking only "Stripe", use command: "vendor/bin/mftf run:group PaymentStripe -r"