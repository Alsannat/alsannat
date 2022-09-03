<?php
namespace Wigzo\TagManager\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use \Magento\Framework\HTTP\Adapter\CurlFactory;
use \Magento\Framework\ObjectManagerInterface;
use Magento\Framework\HTTP\Client\Curl;
//use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;

class Checkout implements ObserverInterface
{

    private $productUrlRewriteResource;
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    private $curlFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;
    private $cookieManager;
    /**
     * @var PsrLoggerInterface
     */
    private $logger;
    private $_header;
    private $scopeInterface;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    private $redirect;
    /**
     * @var CustomerCart
     */
    private $cart;

    public function __construct(ObjectManagerInterface $objectManager,
                                CurlFactory $curlFactory,
                                PsrLoggerInterface $logger,
                                \Magento\Framework\App\Response\RedirectInterface $redirect,
                                Session $session
    )
    {
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->objectManager = $objectManager::getInstance();
        $this->cookieManager = $objectManager::getInstance()->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $this->_header = $objectManager::getInstance()->get('Magento\\Framework\\HTTP\\Header');
        $this->scopeInterface = $objectManager::getInstance()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->redirect = $redirect;
        $this->_session = $session;
    }

    private function _prepareEvent($events, $productIds){
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
        if (in_array('checkoutstarted', $events)) {
            $this->checkoutStarted($productIds, $wigzoData);
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

    public function checkoutStarted($productIds, $wigzoData){
//         $this->logger->info("inside checkoutStarted");

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
            "eventval" => $productIds,
            "source" => $source,
            "e" => $pageUuid,
            "_" => $timestamp,
            "eventCategory" => $eventCategory,
            "referrer" => $this->redirect->getRefererUrl(),
            "lang" => $lang
        ];

        $postUrl = $wigzo_host."/learn/" . $orgToken . "/checkoutstarted/" . $cookieID."?u=&_siteid=".$orgToken;
        $this->_postEvent($postUrl, $postData);
    }

    public function execute(Observer $observer) {
        $cartItems = $this->_session->getQuote()->getAllVisibleItems();
//        $this->logger->info("checkout observer caught an event:  ".json_encode($cartItems));
        $productIds = array();
        foreach($cartItems as $item) {
            array_push($productIds, $item->getProductId());
        }
        $this->_prepareEvent(['checkoutstarted'], $productIds);

    }
}