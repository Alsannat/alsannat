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

class BillingAddressManagement
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
        \Magento\Quote\Model\BillingAddressManagement $subject,
        $cartId,
        \Magento\Quote\Api\Data\AddressInterface $address,
        $useForShipping = false
    ) {
        $this->coreSession->start();
        $extAttributes = $address->getExtensionAttributes();
        if (!empty($extAttributes)) {

            try {
                $billLatLong = ['latitude'=>$extAttributes->getLatitude(),'longitude'=>$extAttributes->getLongitude()];
                $this->coreSession->setBillingLatLong($billLatLong);
                $address->setLatitude($extAttributes->getLatitude());
                $address->setLongitude($extAttributes->getLongitude());
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
            
        }
    }
}
