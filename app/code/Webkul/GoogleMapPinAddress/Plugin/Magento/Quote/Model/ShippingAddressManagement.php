<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_GoogleMapPinAddress
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\GoogleMapPinAddress\Plugin\Magento\Quote\Model;

class ShippingAddressManagement
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    
     /**
      * @var \Magento\Framework\Session\SessionManagerInterface
      */
    protected $coreSession;

    /**
     * Constructor.
     *
     * @param \Psr\Log\LoggerInterface                             $logger
     * @param \Magento\Framework\Session\SessionManagerInterface   $coreSession
     */
    
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->logger = $logger;
        $this->coreSession = $coreSession;
    }

    public function beforeAssign(
        \Magento\Quote\Model\ShippingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address
    ) {
        $this->coreSession->start();
        $extAttributes = $address->getExtensionAttributes();

        if (!empty($extAttributes)) {

            try {
                $shipLatLong = ['latitude'=>$extAttributes->getLatitude(),'longitude'=>$extAttributes->getLongitude()];
                $this->coreSession->setShippingLatLong($shipLatLong);
                $address->setLatitude($extAttributes->getLatitude());
                $address->setLongitude($extAttributes->getLongitude());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }

        }
    }
}
