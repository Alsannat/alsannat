<?php

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Helper\Data;
use Custom\City\Model\ZipFactory;

abstract class Zip extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Custom\City\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Custom\City\Model\ZipFactory
     */
    protected $_zipFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $dataHelper
     * @param \Custom\City\Model\CityFactory $cityFactory
     * @param ZipFactory $zipFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $dataHelper,
        \Custom\City\Model\CityFactory $cityFactory,
        ZipFactory $zipFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_zipFactory = $zipFactory;
        $this->_cityFactory = $cityFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
    }
	/**
	* Get zip codes by city
	**/
	public function getZipsByCity($city,$countryId,$stateId){
		$zip_codes_options = array();
        if($city!="" && $stateId!="" &&  $countryId!="" ){
            $cities = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('id',$city)
                ->addFieldToFilter('state_id',$stateId)
                ->addFieldToFilter('country_id',$countryId);
			if($cities->count() > 0){
				$city_id = $cities->getFirstItem()->getId();
				 $zip_codes = $this->_zipFactory->create()->getCollection()
                ->addFieldToFilter('city_id',$city_id)
                ->addFieldToFilter('state_id',$stateId)
                ->addFieldToFilter('country_id',$countryId)
                ->addFieldToFilter('status',1);
				$zip_codes->getSelect()
					->order('id DESC');
				if($zip_codes->count() > 0){
					foreach($zip_codes as $zip){
						if($this->getStoreCode() == "sa"){
                            $zip_codes_options[] = $zip->getZipNameAr();
                        }else{
                            $zip_codes_options[] = $zip->getZipName();
                        }
					}
				}
			}
        }elseif($city!="" && $countryId!="" && $stateId==""){
			$cities = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('id',$city)
                ->addFieldToFilter('country_id',$countryId);
			if($cities->count() > 0){	
                $city_id = $cities->getFirstItem()->getId();
				$zip_codes = $this->_zipFactory->create()->getCollection()
					->addFieldToFilter('city_id',$city_id)
					->addFieldToFilter('country_id',$countryId)
					->addFieldToFilter('status',1);
				$zip_codes->getSelect()
					->order('id DESC');
				if($zip_codes->count() > 0){
					foreach($zip_codes as $zip){
                        if($this->getStoreCode() == "sa"){
                            $zip_codes_options[] = $zip->getZipNameAr();
                        }else{
                            $zip_codes_options[] = $zip->getZipName();
                        }
					}
				}
			}
		}
		return array('options' =>$zip_codes_options, 'store_code' => $this->getStoreCode());
	}
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreCode()
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();    
        $storeManager = $objectManager->create("\Magento\Store\Model\StoreManagerInterface");
        return $storeManager->getStore()->getCode();
    }
}