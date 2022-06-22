<?php

namespace Evincemage\City\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class District extends \Magento\Framework\App\Action\Action {

    protected $request;
    protected $districtModelFactory;
    protected $json;
    protected $_storeManager;


    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Evincemage\City\Model\ResourceModel\District\CollectionFactory $districtModelFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->request = $request;
        $this->districtModelFactory = $districtModelFactory;
        $this->json = $json;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context);
    }

    public function execute() {
        /* Create array for return value */
        if(isset($_GET['city_code']))
        {
            $param = $_GET['city_code'];
            $collection = $this->districtModelFactory->create();
            $collection->addFieldToFilter('naquel_city',  array('eq'=>$param));
            //$collection->addFieldToFilter('store_ids',  array('eq'=>$this->_storeManager->getStore()->getId()));
            $response =[];
            $response[] = array('code' => '', 'name' => __('District / Region'));
            $currentStoreLanguage = $this->_urlInterface->getCurrentUrl();

            if (strpos($currentStoreLanguage, '/en/')!== false)
            {
                foreach ($collection as $district)
                {
                    $response[] = array('code'=>$district->getNaquelDistCode(),'name' => $district->getEnDistrictName());     
                }
            }
            else
            {
                foreach ($collection as $district)
                {
                    $response[] = array('code'=>$district->getNaquelDistCode(),'name' => $district->getArDistrictName());     
                }
            }

            $jsonEncode = json_encode($response);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);  //create Json type return object
            $resultJson->setData($jsonEncode);  // array value set in Json Result Data set
        }
        else
        {
            $response = [];
            $response[] = array('code' => '', 'name' => __('District / Region'));
            $jsonEncode = json_encode($response);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);  //create Json type return object
            $resultJson->setData($jsonEncode);  // array value set in Json Result Data set
        }
        
        return $resultJson; // return json object
    }

}

?>