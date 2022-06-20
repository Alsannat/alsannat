<?php 
namespace Evincemage\ProductAttributes\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		$this->storeManager = $storeManager;
		parent::__construct($context);
	}

	public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }
 
    public function getMediaUrl()
    {
        return $this->getBaseUrl() . 'media/';
    }
 
    public function getProductImageUrl($fileName)
    {
        return $this->getMediaUrl() . 'catalog/product/em_images/' . $fileName;
    }
}