<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class City implements \Magento\Framework\Option\ArrayInterface
{ 

    protected $helperData;
    /**
     * Return data from naqel_shipping_naqel_city table  
     * 
     * @return array
     */
    public function __construct(\Naqel\Shipping\Helper\Data $helperData) {
       $this->helperData      = $helperData;
    }
     
    public function toOptionArray()
    {
    
        $countryCode = $this->helperData->getNaqelClientConfig('client_country_code');
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
      $cityData = $connection->fetchAll("SELECT * FROM naqel_shipping_naqel_city WHERE country_code = '" . $countryCode . "'");
      $optionsArray = array(); 
      $optionsArray[''] = "Select City";
      foreach ($cityData as $key => $city) 
      {
        $optionsArray[$city['code']] = $city['city_name'];      
      }
      return $optionsArray;
    }


}