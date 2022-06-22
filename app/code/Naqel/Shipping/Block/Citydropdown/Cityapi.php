<?php


namespace Naqel\Shipping\Block\Citydropdown;

use Magento\Framework\View\Element\Template;

class Cityapi extends Template
{

    private $configResolver;
    protected $_config;

    public function __construct(
        Template\Context $context,
     /*   \Bluethink\Addressvalidate\Model\Config $config,*/
        array $data = []
    ) {
        parent::__construct($context, $data);
        //$this->_config = $config;
    }
    /*public function getConfigValue($path){
		return $this->_config->getCurrentStoreConfigValue($path);
	}
	public function isEnabled(){
		$isEnabled=$this->getConfigValue('addressvalidate/addressvalidator/active');
		if ($isEnabled == 0) {
			return false;
		}else{
			return true;
		}	
	}
	public function isGoogleApiEnabled(){
		$isGoogleApiEnabled=$this->getConfigValue('addressvalidate/addressvalidator/active_gapi');
		if ($isGoogleApiEnabled == 0) {
			return false;
		}else{
			return true;
		}	
	}
	public function  dataRestEndPoint(){
		$domainValue=$this->getConfigValue('addressvalidate/addressvalidator/addressvalidator_domain');
		return $domainValue;
	}
	public function  getGoogleApiKey(){
		$google_api_key=$this->getConfigValue('addressvalidate/addressvalidator/google_api_key');
		return $google_api_key;
	}*/
}
