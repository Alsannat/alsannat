<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    
    protected $_scopeConfig;
    protected $_naqelLogger;
    const XML_PATH_HELLOWORLD = 'carriers/';
    
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Naqel\Shipping\Logger\Logger $naqelLogger
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Naqel\Shipping\Logger\Logger $naqelLogger)
    {
        $this->scopeConfig  = $scopeConfig;
        $this->_naqelLogger = $naqelLogger;
    }
    
    
    /**
     * @param string
     * @return null
     */
    // log message to /var/log/naqelshipping.log file
    public function naqelLogger($message)
    {
        $this->_naqelLogger->info($message);
    }
    
    /**
     * @param null
     * @return string
     */
    // return store default weight unit
    public function getWeightUnitValue()
    {
        return $this->scopeConfig->getValue('general/locale/weight_unit', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * @param string $code
     * @param int storeId ?
     * @return mix
     */
    // return system config value for naqel api  
    protected function getConfigValue($field, $storeId = null)
    {
        
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * @param string $code
     * @param int storeId ?
     * @return mix
     */
    // return system config value for naqel api
    public function getNaqelClientConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_HELLOWORLD . 'naqelshipping/' . $code, $storeId);
    }
    
    /**
     * @param null
     * @return array
     */
    // return client info array for Naqel API call
    public function getNaqelClientInfo()
    {
        $ClientInfo = array(
            
            'ClientAddress' => array(
                
                'PhoneNumber' => $this->getNaqelClientConfig('client_phone_no'),
                'POBox' => $this->getNaqelClientConfig('client_po_box'),
                'ZipCode' => $this->getNaqelClientConfig('client_zip_code'),
                'Fax' => $this->getNaqelClientConfig('client_fax'),
                'FirstAddress' => $this->getNaqelClientConfig('client_first_address'),
                'Location' => $this->getNaqelClientConfig('client_location'),
                'CountryCode' => $this->getNaqelClientConfig('client_country_code'),
                'CityCode' => $this->getNaqelClientConfig('client_city_code')
                
            ),
            'ClientContact' => array(
                
                'Name' => $this->getNaqelClientConfig('client_name'),
                'Email' => $this->getNaqelClientConfig('client_email'),
                'PhoneNumber' => $this->getNaqelClientConfig('client_phone_no'),
                'MobileNo' => $this->getNaqelClientConfig('client_mobile_no')
                
            ),
            'ClientID' => $this->getNaqelClientConfig('client_id'),
            'Password' => $this->getNaqelClientConfig('password'),
            'Version' => $this->getNaqelClientConfig('naqel_api_version')
            
        );
        
        return $ClientInfo;
    }
    
    /**
     * @param null
     * @return object
     */
    // call Naqel API with PHP  SoapClient 
    public function callNaqelSoapApi()
    {
        $wsdlUrl    = $this->getNaqelClientConfig('naqel_api_endpoint_url');
        $params     = array(
            'encoding' => 'UTF-8',
            'verifypeer' => false,
            'verifyhost' => false,
            'soap_version' => SOAP_1_2,
            'trace' => 1,
            'exceptions' => 1,
            'connection_timeout' => 180,
            'stream_context' => stream_context_create(
                [
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true
                    ]
                ]
            ),
            'cache_wsdl' => WSDL_CACHE_NONE
        );
        
        $soapClient = new \SoapClient($wsdlUrl, $params);
        return $soapClient;
    }
    
    /**
     * @param string
     * @return string
     */
    // return Naqel Currency Id   
    public function getCurrencyId($CurrenciesCode)
    {
        $Currencies = array(
            'SAR' => 1,
            'AED' => 2,
            'USD' => 4,
            'GBP' => 5,
            'OMR' => 6,
            'JOD' => 7,
            'LBP' => 8,
            'BHD' => 9,
            'EGP' => 10,
            'KWD' => 11,
            'CNY' => 12
            
        );
        if (array_key_exists($CurrenciesCode, $Currencies)) {
            return $Currencies[$CurrenciesCode];
        } else {
            return "";
        }
    }   
}