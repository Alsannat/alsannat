<?php
/**
 * @category   Hexasoft
 *
 * @copyright  Copyright (c) IP2Location.com. ( https://www.ip2location.com ).
 */

namespace Hexasoft\IP2LocationCountryBlocker\Observer;

use Magento\Framework\Event\ObserverInterface;

class Blocker implements ObserverInterface
{
	/**
	 * @var \MageWorx\GeoLock\Helper\Data
	 */
	protected $helper;

	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var \Magento\Framework\App\ActionFlag
	 */
	protected $actionFlag;

	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManagerInterface;

	/**
	 * @var \Magento\Framework\App\ResponseFactory
	 */
	private $responseFactory;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	private $url;

	public function __construct(
		\Hexasoft\IP2LocationCountryBlocker\Helper\Data $helper,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Magento\Framework\App\ActionFlag $actionFlag,
		\Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
		\Magento\Framework\UrlInterface $url
	) {
		$this->helper = $helper;
		$this->request = $request;
		$this->responseFactory = $responseFactory;
		$this->url = $url;
		$this->_storeManagerInterface = $storeManagerInterface;
		$this->actionFlag = $actionFlag;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		//echo "here:".$this->helper->isEnabled();exit;
		///$this->helper->isEnabled();
		if (!$this->helper->isEnabled()) {
			return $this;
		}

		if ($this->request->isAjax()) {
			return $this;
		}

		$ipAddress = $this->helper->getClientIp();
		//$ipAddress = '15.184.117.110';
		//$ipAddress = '103.66.233.153';
		//echo $ipAddress;exit;
		//103.66.233.153

		if ($this->isIpBlocked($ipAddress)) {
			$this->deny($observer);

			return $this;
		}

		$countries = $this->helper->getCountries();

		if (!$countries || empty($countries)) {
			return $this;
		}

		$countryCode = $this->getCountryCodeByIp($ipAddress);
		//echo $countryCode;exit;
		if ($countryCode) {
			if (\in_array($countryCode, $countries)) {
				$this->deny($observer);

				return $this;
			}
		}
		$storeID       = $this->_storeManagerInterface->getStore()->getCode();
		$storeName     = $this->_storeManagerInterface->getStore()->getName();
		//$countryCode="BH";
		//echo "sss";
		if ($this->helper->isRedirectionEnabled()) {
			$storeManagerDataList = $this->_storeManagerInterface->getStores();
//echo $countryCode;
			foreach ($storeManagerDataList as $key => $value) {
			//	echo $value['code'].'=='.$countryCode."</br>"; 
				if ( strtoupper($countryCode)=="BH" && $value['code']=='bh_ar') {
					//echo $value['code'].'=='.$countryCode;exit;
					//echo $storeID;
					if($storeID != "bh_ar" && $storeID != "bh_en"  ){
						//echo "asd";die;
							$this->_storeManagerInterface->setCurrentStore("bh_ar");
					}
				}
			}
		}
		$storeID       = $this->_storeManagerInterface->getStore()->getCode();
		$storeName     = $this->_storeManagerInterface->getStore()->getName();
		//echo $storeID.'--'.$storeName;exit;
		return $this;
	}

	public function getCountryCodeByIp($ip)
	{
		$apiKey = $this->helper->getApiKey();

		if ($apiKey) {
			$json = json_decode($this->curl_get_contents('https://api.ip2location.com/v2/?key=' . $apiKey . '&ip=' . $ip));

			if (isset($json->country_code) && \strlen($json->country_code) == 2) {
				return $json->country_code;
			}
		}

		if (is_file($this->helper->getDatabase())) {
			require_once BP . '/app/code/Hexasoft/IP2LocationCountryBlocker/lib/class.IP2Location.php';
			$db = new \IP2Location\Database($this->helper->getDatabase(), \IP2Location\Database::FILE_IO);
			$response = $db->lookup($ip, \IP2Location\Database::ALL);

			if (isset($response['countryCode']) && \strlen($response['countryCode']) == 2) {
				return $response['countryCode'];
			}
		}

		return false;
	}

	public function curl_get_contents($url)
	{
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	protected function deny($observer)
	{
		$action = $observer->getControllerAction();
		$response = $action->getResponse();
		$response->clearBody()->setStatusCode(\Magento\Framework\App\Response\Http::STATUS_CODE_403)->setBody('<html><head><title>403 Forbidden</title></head><body><h1>Forbidden</h1><p>You do not have permission to access this page.</p></body></html>');
		$this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
	}

	public function isIpBlocked($clientIp)
	{
		$list = $this->helper->getIpBlacklist();

		if ($list) {
			foreach ($list as $ip) {
				if ($ip === $clientIp) {
					return true;
				}

				if (strpos($ip, '/') !== false) {
					if ($this->withinCIDR($clientIp, $ip)) {
						return true;
					}
				}

				if (strpos($ip, '*') !== false) {
					$ip = str_replace(['.', '*'], ['\.', '[0-9]*'], $ip);

					if (preg_match('/^' . trim($ip) . '$/', $clientIp)) {
						return true;
					}
				}
			}
		}

		return false;
	}

	protected function withinCIDR($ip, $range)
	{
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
			list($subnet, $bits) = explode('/', $range);
			$ip = ip2long($ip);
			$subnet = ip2long($subnet);
			$mask = -1 << (32 - $bits);
			$subnet &= $mask;

			return ($ip & $mask) == $subnet;
		} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$ip = inet_pton($ip);
			$binary = $this->inetToBits($ip);

			list($subnet, $bits) = explode('/', $range);
			$subnet = inet_pton($subnet);
			$binarynet = $this->inetToBits($subnet);

			$ipBits = substr($binary, 0, $bits);
			$netBits = substr($binary, 0, $bits);

			return $ipBits === $netBits;
		}
	}

	protected function inetToBits($inet)
	{
		$unpacked = unpack('A16', $inet);
		$unpacked = str_split($unpacked[1]);
		$binaryip = '';
		foreach ($unpacked as $char) {
			$binaryip .= str_pad(decbin(\ord($char)), 8, '0', STR_PAD_LEFT);
		}

		return $binaryip;
	}
}
