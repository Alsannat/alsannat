<?php
namespace Wigzo\TagManager\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;
use Exception;
use \Magento\Framework\App\RequestInterface;
use \Magento\CatalogUrlRewrite\Model\ResourceModel\Category\Product as ProductUrlRewriteResource;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Magento\Framework\HTTP\Adapter\CurlFactory;
use \Magento\Framework\ObjectManagerInterface;
use Magento\Framework\HTTP\Client\Curl;

class Catalog implements ObserverInterface
{

    private $productUrlRewriteResource;
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     */
    private $curlFactory;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $requestInterface;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;
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

    public function __construct(ObjectManagerInterface $objectManager,
                                CurlFactory $curlFactory,
                                JsonHelper $jsonHelper,
                                ProductUrlRewriteResource $productUrlRewriteResource,
                                RequestInterface $requestInterface,
                                PsrLoggerInterface $logger,
                                \Magento\Framework\App\Response\RedirectInterface $redirect

    )
    {
        $this->logger = $logger;
        $this->curlFactory = $curlFactory;
        $this->jsonHelper = $jsonHelper;
        $this->requestInterface = $requestInterface;
        $this->objectManager = $objectManager::getInstance();
        $this->productUrlRewriteResource = $productUrlRewriteResource;
        $this->cookieManager = $objectManager::getInstance()->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $this->_header = $objectManager::getInstance()->get('Magento\\Framework\\HTTP\\Header');
        $this->scopeInterface = $objectManager::getInstance()->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->redirect = $redirect;
    }


    private function getIdFromRequestOrPathInfo(){
          // Checking if we are on a product page
          if ($this->requestInterface->getParam('id')) {
              // Regular request like `catalog/product/view/id/8`
              return $this->requestInterface->getParam('id');

          } else {
              // In case url rewrite we should search id in the `url_rewrite` table by path and type
              /** @var string $pathInfo */
              $pathInfo         = $this->requestInterface->getPathInfo();
              $preparedPathInfo = ltrim(trim($pathInfo), "/");

              $connection = $this->productUrlRewriteResource->getConnection();
              $table      = $this->productUrlRewriteResource->getTable('url_rewrite');
              $select     = $connection->select();
              $select->from($table, ['entity_id'])
                  ->where('entity_type = :entity_type')
                  ->where('request_path LIKE :request_path');
              $result = $connection->fetchCol(
                  $select,
                  ['entity_type' => 'product', 'request_path' => $preparedPathInfo]
              );

              return isset($result[0]) ? $result[0] : null;
          }

    }

    private function _prepareEvent($events, $object){

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
        if (in_array('productview', $events)) {
//             if (!$object instanceof Magento\Catalog\Model\Product){
//                 $this->logger->info("returning before product view due to object not being an instance of product");
//             }
            $this->productView($object, $wigzoData);
        }
        if (in_array('productindex', $events)) {
//             if (!$object instanceof Magento\Catalog\Model\Product){
//                 $this->logger->info("returning before product view due to object not being an instance of product");
//             }
            $this->productIndex($object, $wigzoData);
        }
        if (in_array('categoryview', $events)) {
//             if (!$object instanceof Magento\Catalog\Model\Category){
//                 $this->logger->info("returning before categoryview due to object not being an instance of category");
//             }
            $this->categoryView($object, $wigzoData);
        }

    }

    private function _postEvent($postUrl, $postParams, $postData){
        try {
            $http = $this->curlFactory->create();
            $config = ['timeout' => 2, 'useragent' => $this->_header->getHttpUserAgent()];
            $http->setConfig($config);
            $http->write(\Zend_Http_Client::POST, $postUrl, '1.1',
                [ "Content-Type:application/json", "Authorization: Basic cm9iaW5sYXVAcG9zdG5ldC5jb20uYXU6TWFnaWNtYW4yMTEwNzU=" ], //http_build_query($postParams, '', '&'),
                json_encode($postData));
            $result = $http->read();
            $body = \Zend_Http_Response::extractBody($result);
            $res_code = \Zend_Http_Response::extractCode($result);
            if (!empty($body)){/* convert JSON to Array */ $this->logger->info(json_encode($body)); }
            else if (empty($body)) { $this->logger->info("empty body unable to decode"); }
        } catch (\Exception $e) {
            $debugData['http_error'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
            $this->logger->info(json_encode($debugData));
            throw $e;
        }
        finally {
            $http->close();
        }
    }

    public function productView($product, $wigzoData){
//         $this->logger->info("inside productView");

        $orgToken = $wigzoData["orgToken"] ;
        $lang = $wigzoData["lang"] ;
        $timestamp = $wigzoData["timestamp"] ;
        $eventCategory = $wigzoData["eventCategory"] ;
        $source = $wigzoData["source"] ;
        $cookieID = $wigzoData["cookieID"] ;
        $pageUuid = $wigzoData["pageUuid"] ;
        $wigzo_host = $wigzoData["wigzo_host"] ;

        $postParams = array([
            'u' => $product->getProductUrl(),
            "_siteid"=>$orgToken
        ]);

        $postData = [
            "pageuuid" => $pageUuid,
            "eventval" => $product->getProductUrl(),
            "source" => $source,
            "e" => $pageUuid,
            "_" => $timestamp,
            "eventCategory" => $eventCategory,
            "referrer" => $this->redirect->getRefererUrl(),
            "lang" => $lang
        ];

//        $postUrl = $wigzo_host."/learn/" . $orgToken . "/productview/" . $cookieID;
        $postUrl = $wigzo_host."/learn/" . $orgToken . "/productview/" . $cookieID."?u=".$product->getProductUrl()."&_siteid=".$orgToken;

        $this->_postEvent($postUrl, $postParams, $postData);
    }

    public function productIndex($product, $wigzoData){
//         $this->logger->info("inside productIndex");

        $orgToken = $wigzoData["orgToken"] ;
        $lang = $wigzoData["lang"] ;
        $timestamp = $wigzoData["timestamp"] ;
        $eventCategory = $wigzoData["eventCategory"] ;
        $source = $wigzoData["source"] ;
        $cookieID = $wigzoData["cookieID"] ;
        $pageUuid = $wigzoData["pageUuid"] ;
        $wigzo_host = $wigzoData["wigzo_host"] ;


        $store = $this->objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore();
        $imageUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage();
        $ip =  base64_encode($this->objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress')->getRemoteAddress());
        $priceHelper = $this->objectManager->create('Magento\Framework\Pricing\Helper\Data');
        $price = $product->getPrice();
        $formattedPrice = $priceHelper->currency($price, true, false);

        //Post data to be sent in the request.
        $postData = [
            "canonicalUrl"=>$product->getProductUrl(),
            "title"=>$product->getName(),
            "description"=> $product->getDescription(),
            "productId"=>$product->getId(),
            "image"=>$imageUrl,
            "lang"=>$lang,
            "eventCategory"=>$eventCategory,
            "_"=>$timestamp,
            "e"=>$pageUuid,
            "pageuuid"=>$pageUuid,
            "source"=>$source,
            "price"=>$formattedPrice,
            "mRemoteAddress"=> $ip
        ];

        $postParams = array([
            'u' => $product->getProductUrl(),
            "_siteid"=>$orgToken
        ]);
//        $postUrl = $wigzo_host."/learn/" . $orgToken . "/product/push/" . $cookieID;
        $postUrl = $wigzo_host."/learn/" . $orgToken . "/product/push/" . $cookieID."?u=".$product->getProductUrl()."&_siteid=".$orgToken;

        $this->_postEvent($postUrl, $postParams, $postData);

    }

    public function categoryView($categoryObject, $wigzoData){
//         $this->logger->info("inside category view, category name is ". $categoryObject->getName());

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
            "eventval" => $categoryObject->getUrl(),
            "source" => $source,
            "e" => $pageUuid,
            "_" => $timestamp,
            "eventCategory" => $eventCategory,
            "referrer" => $this->redirect->getRefererUrl(),
            "lang" => $lang
        ];


        $postParams = array([
            'u' => $categoryObject->getUrl(),
            "_siteid"=>$orgToken
        ]);
//        $postUrl = $wigzo_host."/learn/" . $orgToken . "/categoryview/" . $cookieID;
        $postUrl = $wigzo_host."/learn/" . $orgToken . "/categoryview/" . $cookieID."?u=".$categoryObject->getUrl()."&_siteid=".$orgToken;

        $this->_postEvent($postUrl, $postParams, $postData);

    }

    public function execute(Observer $observer) {

        //catalog_controller_product_view event cannot be used in conjunction
        // with FPC cache since Magento will not raise it after the first visit.
        // That's why I am using controller_front_send_response_before

        $idFromRequest = $this->getIdFromRequestOrPathInfo();
        if ($idFromRequest){
            $product = $this->objectManager->get('Magento\Catalog\Model\Product')->load($idFromRequest);

            $catalogHelperData = $this->objectManager->get('Magento\Catalog\Helper\Data');
            $categoryObject = $catalogHelperData->getCategory();

//            if (null == $categoryObject){
//                // TODO: comment it out after testing
//                $categoryObject = $this->objectManager->create('Magento\Catalog\Model\Category')->load($idFromRequest);
//            }
            if($product != null && $categoryObject != null){
                //this is first uncached visit on this page, it maybe either product page or category page, lets find out
                try {
                    /* Some logic that could throw an Exception */
                    $categoryId = $categoryObject->getId();
                    $productCatIds = $product->getCategoryIds();
                    // now that we have category_id and product_cat_ids
                    // we can compare them to determine if this is a product page or category page
                    if (in_array($categoryId, $productCatIds)) {
//                         $this->logger->info("bhai ye to product page hai, product ka name hai ". $product->getName());
//                        $this->productView($product->getId());
//                        $this->productIndex($product->getId());
                        $events = array("productview", "index");
                        $this->_prepareEvent($events, $product);
//                        $this->_prepareEvent("index", $product);
                    }
                    else if (!in_array($categoryId, $productCatIds)) {
                        $events = array("categoryview");
                        $this->_prepareEvent($events, $categoryObject);
//                        $this->categoryView($categoryObject);
                    }
                } catch (Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
            else if ($product != null && $categoryObject == null){
                // cached revisit observed, this must be a product because we have product created from requestId
//                 $this->logger->info("cached revisit observed, this must be a product because we have product created from requestId, product name is: ". $product->getName());
//                $this->productView($product->getId());
//                $this->productIndex($product->getId());
                $events = array("productview", "index");
                $this->_prepareEvent($events, $product);
            }
        }
    }
}