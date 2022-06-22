<?php

/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */

namespace Naqel\Shipping\Controller\Adminhtml\AfterSalesReturn;

use Naqel\Shipping\Model\WaybillFactory;

ob_start();

class Index extends \Magento\Backend\App\Action
{
    protected $_pageFactory;
    protected $_WaybillFactory;
    protected $request;
    protected $helperData;
    protected $jsonResultFactory;
    protected $orderFactory;
    protected $_shipping;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory
     * @param \Naqel\Shipping\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory,
        \Naqel\Shipping\Helper\Data $helperData,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Naqel\Shipping\Model\ShippingFactory  $shippingFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        $this->helperData = $helperData;
        $this->_WaybillFactory = $WaybillFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->orderFactory = $orderFactory;
        $this->_shipping   = $shippingFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        //$waybill_no =  $this->request->getParam('waybill_no');
        $entity_id  = $this->request->getParam('entity_id');
        if (isset($entity_id) && $entity_id != '') {            
            $order = $this->orderFactory->create();
            $order->load($entity_id);
            $ClientInfo = $this->helperData->getNaqelClientInfo();

            $PicesCount            = 1;
            $Reference1            = '';
            $DeliveryInstruction   = $order->get_customer_note();
            $BillingType = '';
            $LoadTypeID = 66; // get_option('naqel_load_type');
            $create_booking = $this->helperData->getNaqelClientConfig('create_booking');            
            $CountryCode = $this->helperData->getNaqelClientConfig('client_country_code');
            $w_items = $order->getAllItems();
            $weight = 0;
            foreach ($w_items as $w_item) {
                $weight += ($w_item->getWeight() * intval($w_item->getQtyOrdered()));
            }
            $weight = ($weight > 0) ? $weight : 1;
            if ($create_booking != 1) {
                $create_booking = false;
            } else {
                $create_booking = true;
            }
            $payment_code    = $order->getPayment()->getMethodInstance()->getCode();            
            //commented
            if ($payment_code == 'cashondelivery') {                
			          $BillingType = '5';
            } else {                
			          $BillingType = '1';
            }
            $customerDetail = $order->getShippingAddress()->getData();
            $ConsigneeName = $customerDetail['firstname'] . " " . $customerDetail['lastname'];
            $CityCode   = $this->getNaqelCityMappingData($customerDetail['city']);
            $customerStreet = $customerDetail['street'];            
            if ($customerDetail['country_id'] == 'SA') {
                $customerCountryId = $CountryCode;
            } else {
                $customerCountryId = $customerDetail['country_id'];
            }
            $ConsigneeAddress = $customerStreet . " " . $customerDetail['city'] . ", " . $customerDetail['postcode'] . " " . $customerDetail['region'] . ", " . $customerCountryId;
            $refNo = $entity_id . '_M_' . date("dmy", time());            
            $consigneeInfo = array(
                'ConsigneeName' => $ConsigneeName,
                'Email' => $customerDetail['email'],
                'Mobile' => $customerDetail['telephone'],
                'PhoneNumber' => $customerDetail['telephone'],
                'Address' => $ConsigneeAddress,
                'CountryCode' => $customerCountryId,
                'CityCode' => $CityCode, //$order->get_billing_city()
            );
            $waybillSurcharge = array("SurchargeIDList" => array('int' => 9));
            $apiRequestData = array(
                "_AsrManifestShipmentDetails" => array(
                    'ClientInfo' => $ClientInfo,
                    "ConsigneeInfo" => $consigneeInfo,
                    // 'InvoiceDate' => date('Y-m-d H:i:s'),
                    'CreateBooking' => $create_booking,
                    'CurrencyID' => '1', //$order_currency_code,
                    'BillingType' => $BillingType,
                    'PicesCount' => $PicesCount,
                    'Weight' => $weight,
                    'RefNo' => $refNo,
                    'DeliveryInstruction'  =>  $DeliveryInstruction,
                    'LoadTypeID'  =>  $LoadTypeID,
                    'GoodDesc' => '',
                    'WaybillNo' => '',
                    'OriginWaybillNo' => '',
                    'DeclareValue' => 0,
                    'PickUpDate' => date('Y-m-d', strtotime("+2 days")),
                    'InsuredValue' => 0,
                    'GoodsVATAmount' => 0,
                    'Reference1' => $Reference1,
                    'WaybillSurcharge' => $waybillSurcharge
                )
            );
            $soapClient = $this->helperData->callNaqelSoapApi();
            $response = $soapClient->CreateWaybillForASR($apiRequestData);
            
            $waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData();
            $data=array('asr_waybill_no'=> $response->CreateWaybillForASRResult->WaybillNo);
            $waybillmodal = $this->_WaybillFactory->create();                       
            $waybillmodal->load( $waybill_data[0]['waybill_id']);        
            $waybillmodal->addData($data);
            $waybillmodal->save();
            
            $result = $this->jsonResultFactory->create();
            $result->setData($response->CreateWaybillForASRResult);
            return $result;
        }
    }
    
    protected function getNaqelCityMappingData($cityName)
    {
        $collection = $this->_shipping->create()->getCollection()
            ->addFieldToFilter(
            array('client_city_name', 'client_city_name_ar'),
            array(array('eq' => $cityName), array('eq' => $cityName))
            );
        $data = $collection->getData();
        if(count($data) > 0) {
            return $data[0]['code'];
        } else {
            return $this->returnBackWithError('City name "'. $cityName .'" not mapped with Naqel');
        }
    }

    public function returnBackWithError($errorMessage)
    {
        $this->helperData->naqelLogger($errorMessage);
        $this->messageManager->addError("Naqel Shipping :- " . substr($errorMessage,0,215));
        $redirectUrl = $this->redirect->getRedirectUrl();
        $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        die();
    }
}
