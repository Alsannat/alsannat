<?php

namespace Wigzo\TagManager\Block\Adminhtml\Index;
class Index extends \Magento\Backend\Block\Widget\Container
{
	
	protected $request;

	public function gen_uuid () {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
            mt_rand( 0, 0xffff ),
            mt_rand( 0, 0x0fff ) | 0x4000,
            mt_rand( 0, 0x3fff ) | 0x8000,
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }
	 public function __construct(\Magento\Backend\Block\Widget\Context $context,
			 					\Magento\Config\Model\ResourceModel\Config $resourceConfig,
								array $data = [])
    {
		$scopeInterface = $context->getScopeConfig();
		$authToken = $scopeInterface->getValue ("admin/wigzo/key");

        if (NULL == $authToken) {
			$authToken = $this->gen_uuid ();
		    $resourceConfig->saveConfig ('admin/wigzo/key', $authToken, 'default', 0);
        }

        $data["wigzo_host"] = "https://app.wigzo.com";
		$resourceConfig->saveConfig ('admin/wigzo/host', $data["wigzo_host"], 'default', 0);
		
		$data["tracker"]="https://tracker.wigzopush.com";
		$resourceConfig->saveConfig ('admin/wigzo/tracker', $data["tracker"], 'default', 0);
        $data["wigzo_authtoken"] = $authToken;
		$val = $data["wigzo_authtoken"] ;
		
        parent::__construct($context, $data);
   }
}
