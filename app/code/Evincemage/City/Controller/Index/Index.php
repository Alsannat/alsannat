<?php

namespace Evincemage\City\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Locale\Resolver;

class Index extends \Magento\Framework\App\Action\Action {

    protected $request;
    protected $courierModelFactory;
    protected $json;
    protected $_storeManager;


    public function __construct(
        Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Evincemage\City\Model\ResourceModel\Grid\CollectionFactory $courierModelFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\Data\StoreInterface $store,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->request = $request;
        $this->courierModelFactory = $courierModelFactory;
        $this->json = $json;
        $this->_storeManager = $storeManager;
        $this->_store = $store;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context);
    }

    public function execute() {
        /* Create array for return value */
        if(isset($_GET['country_code']))
        {
            $param = $_GET['country_code'];
            $collection = $this->courierModelFactory->create();
            $collection->addFieldToFilter('country_code',  array('eq'=>$param));
            $collection->addFieldToFilter('store_ids',  array('in'=>[$this->_storeManager->getStore()->getId(),"0"]));
            $response =[];
            $response[] = array('code' => '', 'name' => __('City'));
            $currentStoreLanguage = $this->_urlInterface->getCurrentUrl();
            
            if (strpos($currentStoreLanguage, '/en/')!== false)
            {
                foreach ($collection as $city)
                {
                    $response[] = array('code'=>$city->getNaquelCity(),'name' => $city->getCityEn());     
                }
            }
            else
            {
                foreach ($collection as $city)
                {
                    $response[] = array('code'=>$city->getNaquelCity(),'name' => $city->getCityAr());     
                }

            }

            

            $jsonEncode = json_encode($response);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);  //create Json type return object
            $resultJson->setData($jsonEncode);  // array value set in Json Result Data set
        }
        else
        {
            $response = [];
            $response[] = array('code' => '', 'name' => __('Please Select'));
            $jsonEncode = json_encode($response);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);  //create Json type return object
            $resultJson->setData($jsonEncode);  // array value set in Json Result Data set
        }
        
        return $resultJson; // return json object
    }

}

?>