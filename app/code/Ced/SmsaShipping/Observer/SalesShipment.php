<?php

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_SmsaShipping
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\SmsaShipping\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesShipment implements ObserverInterface
{
     protected $_registry = null;
     protected $_storeManager;
     protected $_moduleReader;

   public function __construct (        
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Dir\Reader $moduleReader
    ) {
        $this->_objectManager=$objectManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_registry = $registry;
        $this->_storeManager = $storeManager;
        $this->_moduleReader = $moduleReader;
    }
 
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
            $invoice = $observer->getEvent()->getInvoice();
            $shipment = $observer->getEvent()->getShipment();   
            $order = $shipment->getOrder();
            $invoice_id = null;
            foreach($order->getInvoiceCollection() as $OI)
                $invoice_id = $OI->getIncrementId();
//            file_put_contents("/home/alsannat1/public_html/111.txt", json_encode(["order" => $order, "invoice_coolection" => $invoice_id]));
            $user = $this->_objectManager->get('\Magento\Backend\Model\Auth\Session'); 
            $userFirstname = $user->getUser()->getFirstname();
            $userLastname = $user->getUser()->getLastname();
            $store_contact = $this->_scopeConfig->getValue('general/store_information/phone', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $store_country = $this->_scopeConfig->getValue('shipping/origin/country_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $store_region = $this->_scopeConfig->getValue('shipping/origin/region_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $store_city = $this->_scopeConfig->getValue('shipping/origin/city', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $store_street1 = $this->_scopeConfig->getValue('shipping/origin/street_line1', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $store_street2 = $this->_scopeConfig->getValue('shipping/origin/street_line2', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $customer_billing_address = $order->getShippingAddress();
            $dest_add  = $customer_billing_address->getStreet();
            $totalItems     = 0;
            $description = "";
            $totalWeight = 0;
            $totalPrice =0;
            $qty = 0;
            $items = $order->getAllItems();
            $store_street1 = "Riyadh, Al Shifa Bin Tymiah Rd";
            $postData = $_POST;
            if(isset($postData['invoice']) && count($postData['invoice']))
                $postItems = $postData['invoice']['items'];
            else
                $postItems = $postData['shipment']['items'];
        
            foreach($items as $item){
                
                if(isset($postItems[$item->getItemId()])){
                    if($item->getWeight() != 0){
                        $qty = $postItems[$item->getItemId()];
                        $weight =  $item->getWeight() * $qty;
                    } else {
                        $weight =  0.5 * $qty;
                    }

                    $totalWeight    += $weight;
                    $totalItems     += $qty;
                    $totalPrice  += $item->getBaseRowTotal();
                    $description .= $item->getProduct()->getSku()." | "; 
                }     
            }
    
            //$description = mb_substr($description, 0, 48, 'utf8');
            $shippingMethod = $order->getShippingMethod();
            $s = explode("~",$shippingMethod);
            $shippingMethod=$s[0];
            if($shippingMethod == "smsashipping_smsashipping")
            {
                
                $params = array();
                $pass_key = $this->_scopeConfig->getValue('carriers/smsashipping/passkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $user_name = $userFirstname;
                $po_box = $this->_scopeConfig->getValue('carriers/smsashipping/po_box', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $params["passKey"] = $pass_key;
                $params["refNo"] = $order->getIncrementId(). ($invoice_id ? "(".$invoice_id.")" : null);
                $params["sentDate"] = date('Y-m-d h:i:s a');
                $params["idNo"] = $order->getIncrementId();
                $params["cName"] = $customer_billing_address->getName();
                $params["cntry"] = $customer_billing_address->getCountryId();
                $params["cCity"] = $customer_billing_address->getCity();
                $params["cZip"] = '11111';//$customer_billing_address->getPostcode();
                $params["cPOBox"] = $po_box;
                $params["cMobile"] = $customer_billing_address->getTelephone();
                $params["cTel1"] = $customer_billing_address->getTelephone();
                $params["cTel2"] = $customer_billing_address->getTelephone();
                $params["cAddr1"] = isset($dest_add[0])?$dest_add[0]:'';
                $params["cAddr2"] = isset($dest_add[1])?$dest_add[1]:'';
                $params["shipType"] = 'DLV';
                $params["PCs"] = $totalItems;
                $params["cEmail"] = $customer_billing_address->getEmail();
                $params["carrValue"] = 0;
                $params["carrCurr"] = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                if($order->getPayment()->getMethod() == 'cashondelivery'){
                    $params["codAmt"] = $order->getGrandTotal();
                }else{
                    $params["codAmt"] = 0;

                }                
                $params["weight"] = $totalWeight;
                $params["custVal"] = $totalPrice;
                $params["custCurr"] = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                $params["insrAmt"] = 0;
                $params["insrCurr"] = $this->_storeManager->getStore()->getCurrentCurrencyCode();
                $params["itemDesc"] = $description;
                $params["sName"] = $userFirstname.' '.$userLastname;
                $params["sContact"] = $store_contact;
                $params["sAddr1"] = $store_street1;
                $params["sAddr2"] = $store_street2;
                $params["sCity"] = $store_city;
                $params["sPhone"] = $store_contact;
                $params["sCntry"] = $store_country;
                $params["prefDelvDate"] = '';
                $params["gpsPoints"] = '';
                
                try {
                    $wsdlPath = $this->_moduleReader->getModuleDir('etc', 'Ced_SmsaShipping') . '/'. 'wsdl';
                    $wsdl = $wsdlPath . '/' . 'SMSAwebService.xml';
                    $client = new \SoapClient($wsdl, array('trace' => 1)); 
                    $result = $client->addShipment($params);
                    $awbno = $result->addShipmentResult;  
                    if (strpos($awbno, 'Failed') !== false) {
                        throw new \Exception($awbno);
                    }   
                    $shipment = $observer->getEvent()->getShipment();
                    $track = $this->_objectManager->create(
                    'Magento\Sales\Model\Order\Shipment\Track'
                                                )->setNumber(
                                                    $awbno
                                                )->setCarrierCode(
                                                    'smsashipping'
                                                )->setTitle(
                                                    'Smsa Shipping'
                                                );
                    $shipment->addTrack($track);
                }
                catch(\Exception $e)
                {//print_r($e->getMessage());die('aaaaaaa');
                    throw new \Exception(__('Something went wrong while saving shipment.'));
                }
            }       
    }
}
