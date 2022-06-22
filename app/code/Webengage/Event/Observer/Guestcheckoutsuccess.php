<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Guestcheckoutsuccess implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * Guestcheckoutsuccess constructor.
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\RequestInterface $request
     * @param Data $helper
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\RequestInterface $request,
        Data $helper)
    {
        $this->helper = $helper;
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $billingAddress = !empty($order->getBillingAddress()) ? (object)$order->getBillingAddress()->getData() : (!empty($order->getShippingAddress()) ? (object)$order->getBillingAddress()->getData() : '');

        if ($billingAddress != '' && $this->helper->getLicenceInfo() != '') {
            ?>
            <script>
                var sdkWeLoaded = setInterval(getWebSDK, 250);
                function getWebSDK() {
                    if (webengage && webengage.user && webengage.user.getAnonymousId && webengage.state && webengage.state.getSession) {
                        webengage.user.login("<?php echo $billingAddress->email; ?>");
                        webengage.user.setAttribute({
                            "we_first_name" : "<?php echo $billingAddress->firstname; ?>",
                            "we_last_name" : "<?php echo $billingAddress->lastname; ?>",
                            "we_phone" : "<?php echo $billingAddress->telephone; ?>",
                            "postal_code" : "<?php echo $billingAddress->postcode; ?>",
                            "we_email" : "<?php echo $billingAddress->email; ?>",
                        });
                        clearInterval(sdkWeLoaded);
                    }
                }
            </script>
            <?php
        }

    }

}
