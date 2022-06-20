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
    namespace Webkul\GoogleMapPinAddress\Helper;

    use Magento\Framework\App\Config\ScopeConfigInterface;
    use Magento\Framework\App\Helper\Context;
    use Magento\Framework\View\Element\Block\ArgumentInterface;

class MapData extends \Magento\Framework\App\Helper\AbstractHelper implements ArgumentInterface
{
    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;
       
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Directory\Helper\Data
     */
    protected $dirctyHlprData;

    /**
     * @var \Magento\Customer\Helper\Address
     */
    protected $custmrHelprAddress;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface   $scopeConfig
     * @param \Magento\Directory\Helper\Data                       $dirctyHlprData
     * @param \Magento\Customer\Helper\Address                     $custmrHelprAddress
     */

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Helper\Data $dirctyHlprData,
        \Magento\Customer\Helper\Address $custmrHelprAddress
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->dirctyHlprData = $dirctyHlprData;
        $this->custmrHelprAddress = $custmrHelprAddress;
        parent::__construct($context);
    }
    /**
     * Get Api Key
     *
     * @return string $apiKey
     */

    public function getApiKey()
    {
        $apiKey = $this->scopeConfig->getValue('googlemappinaddress/gmpa_settings/api_key');
        return $apiKey;
    }

    /**
     * Get Module Status
     *
     * @return integer $moduleStatus
     */
    public function getModuleStatus()
    {
        $moduleStatus = $this->scopeConfig->getValue('googlemappinaddress/gmpa_settings/active');
        return $moduleStatus;
    }
    /**
     * Get Directory Data
     *
     * @return object
     */
    public function getDirectoryData()
    {
        return $this->dirctyHlprData;
    }

    /**
     * Get Customer Helper Address
     *
     * @return object
     */
    public function getCustomerHelAdd()
    {
        return $this->custmrHelprAddress;
    }
}
