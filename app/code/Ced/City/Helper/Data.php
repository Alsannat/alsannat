<?php
 
namespace Ced\City\Helper;
 
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
 
class Data extends AbstractHelper
{
	public function getConfig($config_path)
	{
		return $this->scopeConfig->getValue(
				$config_path,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
				);
	}
}