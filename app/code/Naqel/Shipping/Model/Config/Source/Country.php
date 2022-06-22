<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class Country implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     *  Return array data from naqel_shipping_naqel_city table   
     *
     * @return array
     */
    public function toOptionArray()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
         $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 

      $cityData = $connection->fetchAll("SELECT * FROM naqel_shipping_naqel_city");
       
      $optionsArray = array(); 
      $optionsArray[''] = "Select Country Code";

      foreach ($cityData as $key => $city) 
      {
        $optionsArray[$city['country_code']] = $city['country_code'];      
      }

      
      return $optionsArray;


    }
}