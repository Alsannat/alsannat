<?php


namespace Wigzo\TagManager\Observer;


use Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\HTTP\Adapter\CurlFactory;
use \Magento\Framework\ObjectManagerInterface;

class CustomerAuthenticated implements ObserverInterface
{


    protected $_customerRepositoryInterface;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;
    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var CurlFactory
     */
    private $curlFactory;
    private $objectManager;
    private $cookieManager;
    private $scopeInterface;
    private $_header;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Psr\Log\LoggerInterface $logger,
        ObjectManagerInterface $objectManager,
        CurlFactory $curlFactory

    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->redirect = $redirect;
        $this->addressRepository = $addressRepository;
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->objectManager = $objectManager::getInstance();
        $this->cookieManager = $objectManager::getInstance()->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $this->_header = $objectManager::getInstance()->get('Magento\\Framework\\HTTP\\Header');
        $this->scopeInterface = $objectManager::getInstance()->create('\Magento\Framework\App\Config\ScopeConfigInterface');

    }

    private function _prepareEvent($events, $customer){
        $enabled = $this->scopeInterface->getValue("admin/wigzo/enabled");
        if ($enabled == NULL || $enabled == "false") {
            return;
        }
        $wigzo_host = $this->scopeInterface->getValue ("admin/wigzo/host");
        if($wigzo_host == "" || NULL == $wigzo_host)
        {
            $wigzo_host = "https://app.wigzo.com";
        }
        $orgToken = $this->scopeInterface->getValue("admin/wigzo/orgId");
        $lang = $this->scopeInterface->getValue("general/locale/code");
        $timestamp = date('Y-m-d H:i:s');
        $eventCategory = "EXTERNAL";
        $source = "web";
        $cookieID = $this->cookieManager->getCookie('WIGZO_LEARNER_ID');
        $pageUuid = $this->cookieManager->getCookie('PAGE_UUID');
        if($cookieID == null || $cookieID =="" || $pageUuid == null || $pageUuid =="")
        {
            $this->logger->info("returning because cookieID: $cookieID, pageUuid: $pageUuid");
            return;
        }
        $wigzoData = array(
            "wigzo_host" => $wigzo_host,
            "orgToken" => $orgToken,
            "lang" => $lang,
            "timestamp" => $timestamp,
            "eventCategory" => $eventCategory,
            "source" => $source,
            "cookieID" => $cookieID,
            "pageUuid" => $pageUuid,
        );
        if (in_array('mapuser', $events)) {
            $this->mapUser($customer, $wigzoData);
        }
        if (in_array('mapphone', $events)) {
            $this->mapPhone($customer, $wigzoData);
        }
        if (in_array('mapemail', $events)) {
            $this->mapEmail($customer, $wigzoData);
        }
    }


    private function _postEvent($postUrl, $postData){
        try {
            $http = $this->curlFactory->create();
            $config = ['timeout' => 2, 'useragent' => $this->_header->getHttpUserAgent()];
            $http->setConfig($config);
            $http->write(\Zend_Http_Client::POST, $postUrl, '1.1',
                [ "Content-Type:application/json", "Authorization: Basic cm9iaW5sYXVAcG9zdG5ldC5jb20uYXU6TWFnaWNtYW4yMTEwNzU=" ],
                json_encode($postData));
            $result = $http->read();
        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->logger->info(json_encode($debugData));
            throw $e;
        }
        finally {
            $http->close();
        }
    }



    private function mapEmail($customer, $wigzoData){
//         $this->logger->info("inside mapEmail, email is ". $customer['email']);

        $orgToken = $wigzoData["orgToken"] ;
        $lang = $wigzoData["lang"] ;
        $timestamp = $wigzoData["timestamp"] ;
        $eventCategory = $wigzoData["eventCategory"] ;
        $source = $wigzoData["source"] ;
        $cookieID = $wigzoData["cookieID"] ;
        $pageUuid = $wigzoData["pageUuid"] ;
        $wigzo_host = $wigzoData["wigzo_host"] ;


        $postData = [
            "pageuuid" => $pageUuid,
            "eventval" => $customer['email'],
            "source" => $source,
            "e" => $pageUuid,
            "_" => $timestamp,
            "eventCategory" => $eventCategory,
            "referrer" => $this->redirect->getRefererUrl(),
            "lang" => $lang
        ];

        $postUrl = $wigzo_host."/learn/" . $orgToken . "/mapemail/" . $cookieID."?_siteid=".$orgToken;

        $this->_postEvent($postUrl, $postData);

    }

    private function mapPhone($customer, $wigzoData){
        $this->logger->info("inside mapPhone, phone is ". $customer['phone']);

        $orgToken = $wigzoData["orgToken"] ;
        $lang = $wigzoData["lang"] ;
        $timestamp = $wigzoData["timestamp"] ;
        $eventCategory = $wigzoData["eventCategory"] ;
        $source = $wigzoData["source"] ;
        $cookieID = $wigzoData["cookieID"] ;
        $pageUuid = $wigzoData["pageUuid"] ;
        $wigzo_host = $wigzoData["wigzo_host"] ;


        $postData = [
            "pageuuid" => $pageUuid,
            "eventval" => $customer['phone'],
            "source" => $source,
            "e" => $pageUuid,
            "_" => $timestamp,
            "eventCategory" => $eventCategory,
            "referrer" => $this->redirect->getRefererUrl(),
            "lang" => $lang
        ]; // {"pageuuid":"f517fc28-be58-4dca-b909-d0c5248de7bf","eventval":"9968392739","source":"web","e":"f517fc28-be58-4dca-b909-d0c5248de7bf","_":"2020-09-28T13:21:16.821Z","eventCategory":"EXTERNAL","referrer":"organic","lang":"en"}


        $postUrl = $wigzo_host."/learn/" . $orgToken . "/mapphone/" . $cookieID."?_siteid=".$orgToken;

        $this->_postEvent($postUrl, $postData);

    }

    private function mapUser($customer, $wigzoData){
//         $this->logger->info("inside mapUser, email is ". $customer['email']);

        $orgToken = $wigzoData["orgToken"] ;
        $source = $wigzoData["source"] ;
        $cookieID = $wigzoData["cookieID"] ;
        $wigzo_host = $wigzoData["wigzo_host"] ;

//         $this->logger->info("customer details json is as follows", $customer);

        $postData = [
            "email" => $customer['email'],
            "phone" => $customer['phone'],
            "fullName" => $customer['fullName']
        ]; //{"email":"venkatesh@wigzo.com","phone":"9968392739","fullName":"Venkatesh Khatri"}


        $postUrl = $wigzo_host."/user/map/" . $cookieID . "?orgId" . $orgToken."&_siteid=".$orgToken."&source=".$source."&fullName=".$customer['fullName'];

        $this->_postEvent($postUrl, $postData);
    }





    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $observedCustomer = $observer->getEvent()->getCustomer();


        $customer = $this->_customerRepositoryInterface->getById($observedCustomer->getId());

        // get full name from either login customer object or register request params
        try {
//            $customerfullName = $customer->getName();
            $customerfullName = $customer->getFirstname()." ".$customer->getLastname();
//             $this->logger->info("customerfullName from register". $customerfullName);

        } catch (\Exception  $e) {
//             $this->logger->info("must be a newly register customer, let get name from request params");
            $requestParameters = $observer->getEvent()->getAccountController()->getRequest()->getParams();
            $customerfullName = $requestParameters["firstname"]." ".$requestParameters["lastname"];
        }

        if (isset($customer)){
            $customerInfo = array(
                "id" => $customer->getId(),
                "fullName" => $customerfullName,
                "email" => $customer->getEmail()
            );

            $billingAddressId = $customer->getDefaultBilling();
            //$shippingAddressId = $customer->getDefaultShipping();

            //get default billing address to extract phone number from it
            try {
                $billingAddress = $this->addressRepository->getById($billingAddressId);
                $telephone = $billingAddress->getTelephone();


            } catch (\Exception $e) {
//                 $this->logger->info("cannot get address or telephone from address");
            }

            $customerInfo["phone"] = isset($telephone) && !empty($telephone) ? $telephone : "";

            $eventsToCall = array();
            if(isset($customerInfo)){
                array_push($eventsToCall, "mapuser");
            }
            if((true === isset($customerInfo['email'])) && (!empty($customerInfo['email']))) {
                array_push($eventsToCall, "mapemail");
//                 $this->logger->info("dispatching map email");
            }
            if((true === isset($customerInfo['phone'])) && (!empty($customerInfo['phone']))) {
                array_push($eventsToCall, "mapphone");
            }
            $this->_prepareEvent($eventsToCall, $customerInfo);
        }

    }
}