<?php
namespace Hexasoft\IP2LocationCountryBlocker\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
	const XML_PATH_ENABLED		= 'ip2locationcountryblocker/settings/enabled';
	const XML_PATH_METHOD		= 'ip2locationcountryblocker/settings/method';
	const XML_PATH_DATABASE		= 'ip2locationcountryblocker/settings/databaseLocation';
	const XML_PATH_API_KEY		= 'ip2locationcountryblocker/settings/apiKey';
	const XML_PATH_IP_BLACKLIST = 'ip2locationcountryblocker/settings/ip_blacklist';
	const XML_PATH_ENABLED_REDIRECTION = 'ip2locationcountryblocker/redirection/enable_redirection';
	const XML_PATH_COUNTRIES = 'ip2locationcountryblocker/settings/countries';
	//const XML_PATH_DATABASE = 'ip2locationcountryblocker/settings/database';
	//const XML_PATH_API_KEY = 'ip2locationcountryblocker/settings/api_key';
	const IP_LIST_REGEXP_DELIMITER = '/[\r?\n]+/';


	/**
	  * @var \Magento\Framework\App\Config\ScopeConfigInterface
	  */
	 protected $_scopeConfig;

	/**
	  * @param Context $context
	  * @param ScopeConfigInterface $scopeConfig
	  */
	 public function __construct(
		 Context $context,
		 ScopeConfigInterface $scopeConfig
	 ) {
		 parent::__construct($context);
		 $this->_scopeConfig = $scopeConfig;
	 }

	/**
	  * Check for module is enabled
	  *
	  * @return bool
	  */
	public function isEnabled($store = null)
	{
		return $this->_scopeConfig->getValue(
			self::XML_PATH_ENABLED,
			ScopeInterface::SCOPE_STORE
		);
	}
	/**
	 * @param null $storeId
	 *
	 * @return bool
	 */
	public function isRedirectionEnabled($storeId = null)
	{
		return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED_REDIRECTION, ScopeInterface::SCOPE_STORE, $storeId);
	}
	/**
	  * Get the lookup method
	  *
	  * @return int
	  */
	public function getMethod()
	{
		return $this->_scopeConfig->getValue(
			self::XML_PATH_METHOD,
			ScopeInterface::SCOPE_STORE
		);
	}

	/**
	  * Get the IP2Location database location
	  *
	  * @return string
	  */
	public function getDatabaseLocation()
	{
		return $this->_scopeConfig->getValue(
			self::XML_PATH_DATABASE,
			ScopeInterface::SCOPE_STORE
		);
	}

	/**
	  * Get API Key
	  *
	  * @return string
	  */
	public function getAPIKey()
	{
		return $this->_scopeConfig->getValue(
			self::XML_PATH_API_KEY,
			ScopeInterface::SCOPE_STORE
		);
	}
	/**
	 * @param null $storeId
	 *
	 * @return array|null
	 */
	public function getCountries($storeId = null)
	{
		$countriesRawValue = $this->scopeConfig->getValue(self::XML_PATH_COUNTRIES, ScopeInterface::SCOPE_STORE, $storeId);

		$countriesRawValue = $this->prepareCode($countriesRawValue);

		if ($countriesRawValue) {
			$countriesCode = explode(',', $countriesRawValue);

			return $countriesCode;
		}

		return $countriesRawValue ? $countriesRawValue : null;
	}

	/**
	 * @param null $storeId
	 *
	 * @return array
	 */
	public function getIpBlacklist($storeId = null)
	{
		$rawIpList = $this->scopeConfig->getValue(self::XML_PATH_IP_BLACKLIST, ScopeInterface::SCOPE_STORE, $storeId);

		$ipList = array_filter((array) preg_split(self::IP_LIST_REGEXP_DELIMITER, $rawIpList));

		return $ipList;
	}

	/**
	 * Changes country code to upper case.
	 *
	 * @param string $countryCode
	 *
	 * @return string
	 */
	public function prepareCode($countryCode)
	{
		return strtoupper(trim($countryCode));
	}

	/**
	 * Get Database From Config.
	 *
	 * @param mixed|null $storeId
	 *
	 * @return string
	 */
	public function getDatabase($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_DATABASE, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * Get API Key From Config.
	 *
	 * @param mixed|null $storeId
	 *
	 * @return string
	 */
	/*public function getApiKey($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_API_KEY, ScopeInterface::SCOPE_STORE, $storeId);
	}*/

	public function getClientIp()
	{
		// If website is hosted behind CloudFlare protection.
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		if (isset($_SERVER['X-Real-IP']) && filter_var($_SERVER['X-Real-IP'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
			return $_SERVER['X-Real-IP'];
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			// Get server IP address
			$serverIp = (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : '';
			$ip = trim(current(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])));

			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) && $ip != $serverIp) {
				return $ip;
			}
		}

		return trim($this->remoteAddress->getRemoteAddress());
	}
}
