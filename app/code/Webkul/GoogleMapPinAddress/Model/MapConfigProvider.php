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
namespace Webkul\GoogleMapPinAddress\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class MapConfigProvider implements ConfigProviderInterface
{
   /**
    * @var \Webkul\GoogleMapPinAddress\Helper\MapData
    */
    protected $helperData;

    /**
     * Constructor.
     * @param \Webkul\GoogleMapPinAddress\Helper\MapData     $helperData
     */

    public function __construct(
        \Webkul\GoogleMapPinAddress\Helper\MapData $helperData
    ) {
        $this->helperData = $helperData;
    }
    /**
     * set data in window.checkout.config for checkout page.
     *
     * @return array $options
     */
    public function getConfig()
    {
        $options = [
            'map' => []
        ];
        $options['map']['status'] = $this->helperData->getModuleStatus();
        $options['map']['api_key'] = $this->helperData->getApiKey();
        return $options;
    }
}
