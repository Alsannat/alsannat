<?php

namespace Custom\City\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Result\PageFactory;
use Custom\City\Helper\Data;
use Custom\City\Model\CityFactory;

abstract class City extends Action
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
     * @var \Custom\City\Model\CityFactory
     */
    protected $_cityFactory;

    /**
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param Data $dataHelper
     * @param CityFactory $cityFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        Data $dataHelper,
        CityFactory $cityFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_dataHelper = $dataHelper;
        $this->_cityFactory = $cityFactory;
        $this->scopeConfig = $scopeConfig;
        $this->resultJsonFactory = $resultJsonFactory;
        // $this->_storeManager = $storeManager;
    }
	/**
	* Get cities by state or country
	**/
	public function getCitiesByState($countryId,$stateId){
		
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/dev16__getcities.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Your text message');
        $cities = array();
        $cities_indexes = array();
        $cities_en = array();

        if( $stateId!="" && $countryId!=""){
            $logger->info('!= null');
            $logger->info('$stateId: '.$stateId);$logger->info('$countryId: '.$countryId);
            $cities_options = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('state_id',$stateId)
                ->addFieldToFilter('country_id',$countryId)
                ->addFieldToFilter('status',1);
            $cities_options->getSelect()
                ->order('sort_order ASC');
            $logger->info('count Collection: '.$cities_options->count());
            if($cities_options->count() > 0){
                foreach($cities_options as $city){
                    if($this->getStoreCode() == "sa"){
                        $cities[] = __($city->getCity_ar());
                        $cities_indexes[] = $city->getId();
                        $cities_en[] = $city->getCity();
                    }else{
                        $cities[] = __($city->getCity());
                        $cities_indexes[] = $city->getId();
                        $cities_en[] = $city->getCity();
                    }
                }
            }
        }elseif($stateId=="" && $countryId!=""){
            $logger->info('else');
            $cities_options = $this->_cityFactory->create()->getCollection()
                ->addFieldToFilter('country_id',$countryId)
                ->addFieldToFilter('status',1);
            $cities_options->getSelect()
                ->order('sort_order ASC');
            if($cities_options->count() > 0){
                foreach($cities_options as $city){
                    if($this->getStoreCode() == "sa"){
                        $cities[] = __($city->getCity_ar());
                        $cities_indexes[] = $city->getId();
                        $cities_en[] = $city->getCity();
                    }else{
                        $cities[] = __($city->getCity());
                        $cities_indexes[] = $city->getId();
                        $cities_en[] = $city->getCity();
                    }
                }
            }
        }

		return array('cities'=>$cities,'cities_indexes'=>$cities_indexes,'cities_en' => $cities_en,'store_code' => $this->getStoreCode());
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