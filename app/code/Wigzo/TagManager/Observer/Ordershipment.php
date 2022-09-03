<?php
namespace Wigzo\TagManager\Observer;

use Magento\Framework\Event\ObserverInterface;
class Ordershipment implements \Magento\Framework\Event\ObserverInterface
{
    // protected $_objectManager;
    
    protected $order;

    public function __construct(\Magento\Sales\Model\Order $order)
    {
        $this->order = $order;
    } 
    public function execute(\Magento\Framework\Event\Observer $observer) 
    {
           $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
           
           $scopeInterface = $objectManager->create ('\Magento\Framework\App\Config\ScopeConfigInterface');
           $enabled = $scopeInterface->getValue ("admin/wigzo/enabled");
           
           $wigzo_host = $scopeInterface->getValue ("admin/wigzo/host");//"https://flash.wigzopush.com";
            if($wigzo_host == "" || NULL == $wigzo_host)
            {
                $wigzo_host = "https://app.wigzo.com";
            }
            if (file_exists("/tmp/wigzomode")) {
            $wigzo_host = trim(file_get_contents("/tmp/wigzomode"));
            }
            $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
            $cookieID = $cookieManager->getCookie('WIGZO_LEARNER_ID');		
           
            $orgToken = $scopeInterface->getValue ("admin/wigzo/orgId");
            $lang = $scopeInterface->getValue ("general/locale/code");
            $timestamp = date('Y-m-d H:i:s');
            $eventCategory = "EXTERNAL";
            $source = "web";
            $obj = $objectManager->get('Magento\Framework\HTTP\PhpEnvironment\RemoteAddress');
       		$ip =  base64_encode($obj->getRemoteAddress());
            $this->_header = $objectManager->get('Magento\\Framework\\HTTP\\Header');
			$userAgent = $this->_header->getHttpUserAgent();
           
           $shipment = $observer->getEvent()->getShipment();
           $order = $shipment->getOrder();
           $shippingMethod = $order->getShippingMethod();
           $orderId = $order->getIncrementId();
           $customer = $order->getCustomerName();
           $email = $order->getCustomerEmail();
           $address = $order->getShippingAddress();
           $street = $address->getStreet();
           $city = $address->getCity();
           $postcode = $address->getPostcode();
           $complete_addr = $street[0].'&nbsp'.$street[1] .'&nbsp'.$city .'&nbsp'.$postcode;
           $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
           $grandTotal = $order->getGrandTotal();
           $currencyGrandTotal = $priceHelper->currency($grandTotal, true, false);
           $orderItems = $order->getAllItems();
           $totalqtyOtder =0;
            foreach ($orderItems as $item) {
              $itemQty = $item->getQtyOrdered();
              $totalqtyOtder = $totalqtyOtder+$itemQty;
             
            }
            $postevent = array();
            $postevent['orderId']=$orderId;
            $postevent['customerName']=$customer;
            $postevent['email']=$email;
            $postevent['address']= $complete_addr;
            $postevent['total']=$currencyGrandTotal;
            $postevent['qtyOrder']=$totalqtyOtder;
           
            $postdata = array();
            $postdata['lang']=$lang; 
            $postdata['eventCategory']=$eventCategory;
            $postdata['_']=$timestamp;
            $postdata['e']="";
            $postdata['eventval']=$postevent;
            $postdata['source']=$source;
            $postdata['mRemoteAddress']= $ip;
                      
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $wigzo_host."/learn/" . $orgToken . "/ordershipped/" . $cookieID."?_siteid=".$orgToken);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,1); 
            curl_setopt($ch, CURLOPT_TIMEOUT, 4); //timeout in seconds
            $server_output = curl_exec($ch);
            
            if($server_output == false)
            {
                return;
            }
            else
            {
                $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($response == 200){
                    curl_close($ch);
                }
                else
                {
                    return;
                }
            }
            
    }
}