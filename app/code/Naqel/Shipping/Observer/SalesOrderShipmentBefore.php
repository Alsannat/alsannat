<?php

/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */

namespace Naqel\Shipping\Observer;

use Magento\Framework\Event\ObserverInterface;
use Naqel\Shipping\Model\WaybillFactory;
use Naqel\Shipping\Helper\Data;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;

//***// //***//
//** This Observer will call before shipment to resgister order data into Naqel API and genrate waybill no for order **//
//***// //***//
class SalesOrderShipmentBefore implements ObserverInterface
{

    protected $helperData;
    protected $_WaybillFactory;
    protected $_entity_id;
    protected $_orderObject;
    private $responseFactory;
    protected $shipment;
    private $url;
    protected $redirect;
    protected $messageManager;
    protected $_request;
    protected $_productModel;
    protected $_shipping;

    protected $trackFactory;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Naqel\Shipping\Helper\Data $helperData,
        \Naqel\Shipping\Model\WaybillFactory $WaybillFactory,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Naqel\Shipping\Model\ShippingFactory  $shippingFactory,
        \Magento\Catalog\Model\Product $productModel,
        ShipmentTrackInterfaceFactory $trackFactory
    ) {
        $this->_request        = $request;
        $this->helperData      = $helperData;
        $this->_WaybillFactory = $WaybillFactory;
        $this->messageManager  = $messageManager;
        $this->responseFactory = $responseFactory;
        $this->url             = $url;
        $this->redirect        = $redirect;
        $this->_productModel   = $productModel;
        $this->_shipping       = $shippingFactory;

        $this->trackFactory       = $trackFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $Naqel_Enable = $this->helperData->getNaqelClientConfig('active');
        $shipment = $observer->getEvent()->getShipment();
        $order    = $shipment->getOrder();
        $shipMethod = $order->getShippingMethod();
        if (!$Naqel_Enable || $shipMethod != 'naqelshipping_naqelshipping') {
            return;
        }
        //send data to naqel api
        $generate_piece_bar_code = $this->helperData->getNaqelClientConfig('generate_piece_bar_code');
        $create_booking = $this->helperData->getNaqelClientConfig('create_booking');
        $LoadTypeID = $this->helperData->getNaqelClientConfig('client_loadtype');
        $BillingType = ''; //$this->helperData->getNaqelClientConfig('client_billtype');
        $CurrenyCode = $this->helperData->getNaqelClientConfig('client_currencyid');
        $CurrenyID = $this->helperData->getCurrencyId($CurrenyCode);
        $CountryCode = $this->helperData->getNaqelClientConfig('client_country_code');

        $CODCharge = 0;
        if ($generate_piece_bar_code != 1) {
            $generate_piece_bar_code = false;
        } else {
            $generate_piece_bar_code = true;
        }
        if ($create_booking != 1) {
            $create_booking = false;
        } else {
            $create_booking = true;
        }
        $this->shipment = $shipment;
        $this->_entity_id   = $order->getId();
        $this->_orderObject = $order;
        $increment_id       = $order->getIncrementId();
        $grand_total_price = number_format($order->getGrandTotal(), 3);
        //$weight calculation
        $weight  = 0;
        $w_items = $order->getAllItems();
        foreach ($w_items as $w_item) {
            $weight += ($w_item->getWeight() * intval($w_item->getQtyOrdered()));
        }
        $weight = ($weight > 0) ? $weight : 1;
        if (!$order->getId()) {
            return;
        }
        try {
            //input values for shipment level values
            $is_ensured_value      = false;
            $ensurance_ammount     = 0;
            $custom_duty_ammount   = "";
            $is_custom_duty_pay_by = false;
            $PicesCount            = 1;
            $Reference1            = "";
            $Reference2            = "";
            $goods_vat_ammount     = 0;
            $DeliveryInstruction   = "";
            $declare_value = 0;
            $new = get_object_vars($this->_request->getPost());
            if (isset($new['shipment'])) {
                if (array_key_exists("is_ensured", $new['shipment'])) {
                    $is_insured = $new['shipment']['is_ensured'];
                    if ($is_insured == "yes") {
                        $is_ensured_value  = true;
                        $ensurance_ammount = $new['shipment']['ensurance_ammount'];
                    }
                }
                if (array_key_exists("is_custom_duty_pay_by", $new['shipment'])) {
                    $is_custom_duty_pay_by = $new['shipment']['is_custom_duty_pay_by'];
                    if ($is_custom_duty_pay_by == "yes") {
                        $is_custom_duty_pay_by = true;
                        $custom_duty_ammount   = $new['shipment']['custom_duty_ammount'];
                    }
                }
                //$PicesCount = intval($order->getData('total_qty_ordered'));
                $PicesCount = ($new['shipment']['piece_count'] != '') ? intval($new['shipment']['piece_count']) : 1;
                $Reference1        = $new['shipment']['reference1'];
                $Reference2        = $new['shipment']['reference2'];
                $goods_vat_ammount = $new['shipment']['goods_vat_ammount'];
                $DeliveryInstruction = $new['shipment']['DeliveryInstruction'];
                $declare_value       = $new['shipment']['DeliveredValue'];
            }
            $invoiceNo = 0;
            if ($order->hasInvoices()) {
                foreach ($order->getInvoiceCollection() as $invoice) {
                    $invoiceNo = $invoice->getIncrementId();
                }
            }
            $objectManager        = \Magento\Framework\App\ObjectManager::getInstance();
            $price_currency_model = $objectManager->get('\Magento\Directory\Model\PriceCurrency');
            $order_currency_code  = $order->getOrderCurrencyCode();
            $shipping_method = $order->getShippingMethod();
            $payment_code    = $order->getPayment()->getMethodInstance()->getCode();
            $payment_title   = $order->getPayment()->getMethodInstance()->getTitle();
            //commented

            //naqel_shipping_naqel_shipping old name
            //if ($shipping_method == 'naqelshipping_naqelshipping') {
            if ($shipping_method) {
                $customerDetail = $order->getShippingAddress()->getData();
                $ConsigneeName = $customerDetail['firstname'] . " " . $customerDetail['lastname'];
                $customerCity   = $this->getNaqelCityMappingData($customerDetail['city']);
                $customerStreet = $customerDetail['street'];
                $consignee_national_id = isset($customerDetail['consignee_national_id']) ? $customerDetail['consignee_national_id'] : '0000000000';
                if ($customerDetail['country_id'] == 'SA') {
                    $customerCountryId = $CountryCode;
                } else {
                    $customerCountryId = $customerDetail['country_id'];
                }
                $declare_value = 0;
                // if ($customerCountryId !== $CountryCode && $declare_value == 0) {
                //     return $this->returnBackWithError("Declared value is required for International Shipment");
                // }
                if ($payment_code == 'cashondelivery') {
                    $CODCharge = $grand_total_price;
                    $BillingType = '5';
                    if ($customerCountryId !== $CountryCode) {
                        $declare_value = $grand_total_price;
                    }
                } else {
                    $CODCharge = 0;
                    $BillingType = '1';
                }
                $ConsigneeAddress = $customerStreet . " " . $customerCity . ", " . $customerDetail['postcode'] . " " . $customerDetail['region'] . ", " . $customerCountryId;
                $refNo = $invoiceNo . '_M_' . date("dmy", time());
                $_apiRequiredDataArray = array(
                    'ConsigneeNationalID' => $consignee_national_id,
                    'DeliveryInstruction' => $DeliveryInstruction,
                    'Fax' => $customerDetail['fax'],
                    'Near' => $customerStreet,
                    'ConsigneeName' => $ConsigneeName,
                    'Email' => $customerDetail['email'],
                    'Mobile' => $customerDetail['telephone'],
                    'PhoneNumber' => $customerDetail['telephone'],
                    'Address' => $ConsigneeAddress,
                    'CountryCode' => $customerCountryId,
                    'CityCode' => $customerCity,
                    'RefNo' => $refNo,
                    'InvoiceNo' => $invoiceNo,
                    'InvoiceDate' => date('Y-m-d H:i:s'),
                    'TotalCost' => $grand_total_price,
                    'CurrencyCode' => $order_currency_code,
                    'CurrenyID' => $CurrenyID,
                    'BillingType' => $BillingType,
                    'PicesCount' => $PicesCount,
                    'Weight' => $weight,
                    'CODCharge' => $CODCharge,
                    'CreateBooking' => $create_booking,
                    'isRTO' => false,
                    'GeneratePiecesBarCodes' => $generate_piece_bar_code,
                    'InsuredValue' => $ensurance_ammount,
                    'IsInsurance' => $is_ensured_value,
                    'LoadTypeID' => $LoadTypeID,
                    'DeclareValue' => $declare_value,
                    'GoodDesc' => '',
                    'IsCustomDutyPayByConsignee' => $is_custom_duty_pay_by,
                    'CustomDutyAmount' => $custom_duty_ammount,
                    'GoodsVATAmount' => $goods_vat_ammount,
                    'Reference2' => $Reference2,
                    'Reference1' => $Reference1
                );
                $this->CreateWaybill($_apiRequiredDataArray);
            }
        } catch (Exception $e) {
            $this->returnBackWithError($e->getMessage());
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
        if (count($data) > 0) {
            return $data[0]['code'];
        } else {
            return $this->returnBackWithError('City name "' . $cityName . '" not mapped with Naqel');
        }
    }

    public function CreateWaybill($_apiRequiredDataArray)
    {
        try {
            $ClientInfo         = $this->helperData->getNaqelClientInfo();
            $ConsigneeInfo      = $this->_createConsigneeInfoArray($_apiRequiredDataArray);
            $_CommercialInvoice = $this->_create_CommercialInvoiceArray($_apiRequiredDataArray);
            $createWaybillapiRequestData = array(
                '_ManifestShipmentDetails' => array(
                    'ClientInfo' => $ClientInfo, //array
                    'ConsigneeInfo' => $ConsigneeInfo, //array
                    '_CommercialInvoice' => $_CommercialInvoice, //array
                    'CurrenyID' => $_apiRequiredDataArray['CurrenyID'],
                    'BillingType' => $_apiRequiredDataArray['BillingType'],
                    'PicesCount' => $_apiRequiredDataArray['PicesCount'],
                    'Weight' => $_apiRequiredDataArray['Weight'],
                    'DeliveryInstruction' => $_apiRequiredDataArray['DeliveryInstruction'],
                    'CODCharge' => $_apiRequiredDataArray['CODCharge'],
                    'CreateBooking' => $_apiRequiredDataArray['CreateBooking'],
                    'isRTO' => $_apiRequiredDataArray['isRTO'],
                    'GeneratePiecesBarCodes' => $_apiRequiredDataArray['GeneratePiecesBarCodes'],
                    'InsuredValue' => $_apiRequiredDataArray['InsuredValue'],
                    'IsInsurance' => $_apiRequiredDataArray['IsInsurance'],
                    'LoadTypeID' => $_apiRequiredDataArray['LoadTypeID'],
                    'DeclareValue' => $_apiRequiredDataArray['DeclareValue'],
                    'GoodDesc' => $_apiRequiredDataArray['GoodDesc'],
                    'RefNo' => $_apiRequiredDataArray['RefNo'],
                    'Reference1' => $_apiRequiredDataArray['Reference1'],
                    'Reference2' => $_apiRequiredDataArray['Reference2'],
                    'GoodsVATAmount' => $_apiRequiredDataArray['GoodsVATAmount'],
                    'IsCustomDutyPayByConsignee' => $_apiRequiredDataArray['IsCustomDutyPayByConsignee']
                )
            );
            /*echo "<pre>";
            print_r($createWaybillapiRequestData);
            die();*/
            $this->_callNaqelApi($createWaybillapiRequestData);
        } catch (Exception $e) {
            $this->returnBackWithError($e->getTraceAsString());
        }
    }

    public function _createConsigneeInfoArray($_apiRequiredDataArray)
    {
        try {
            $consigneeInfo = array(
                'ConsigneeNationalID' => $_apiRequiredDataArray['ConsigneeNationalID'],
                'ConsigneeName' => $_apiRequiredDataArray['ConsigneeName'],
                'Email' => $_apiRequiredDataArray['Email'],
                'Mobile' => $_apiRequiredDataArray['Mobile'],
                'PhoneNumber' => $_apiRequiredDataArray['PhoneNumber'],
                'Fax' => $_apiRequiredDataArray['Fax'],
                'Address' => $_apiRequiredDataArray['Address'],
                'Near' => $_apiRequiredDataArray['Near'],
                'CountryCode' => $_apiRequiredDataArray['CountryCode'],
                'CityCode' => $_apiRequiredDataArray['CityCode']
            );
            return $consigneeInfo;
        } catch (Exception $e) {
            $this->returnBackWithError($e->getMessage());
        }
    }

    public function _create_CommercialInvoiceArray($_apiRequiredDataArray)
    {
        try {
            $_commercialInvoiceDetails = $this->_create_CommercialInvoiceDetails($_apiRequiredDataArray);
            $_CommercialInvoice = array(
                'ClientInfo' => $this->helperData->getNaqelClientInfo(),
                'CommercialInvoiceDetailList' => $_commercialInvoiceDetails,
                'RefNo' => $_apiRequiredDataArray['InvoiceNo'],
                'InvoiceNo' => $_apiRequiredDataArray['InvoiceNo'],
                'InvoiceDate' => date('c', strtotime($_apiRequiredDataArray['InvoiceDate'])),
                'Consignee' => $_apiRequiredDataArray['ConsigneeName'],
                'ConsigneeAddress' => $_apiRequiredDataArray['Address'],
                'ConsigneeEmail' => $_apiRequiredDataArray['Email'],
                'MobileNo' => $_apiRequiredDataArray['Mobile'],
                'Phone' => $_apiRequiredDataArray['PhoneNumber'],
                'TotalCost' => $_apiRequiredDataArray['TotalCost'],
                'CurrencyCode' => $_apiRequiredDataArray['CurrencyCode']
            );
            return $_CommercialInvoice;
        } catch (Exception $e) {
            $this->returnBackWithError($e->getMessage());
        }
    }

    public function _create_CommercialInvoiceDetails($_apiRequiredDataArray)
    {
        $i = 0;
        $_commercial = array();
        $orderItems = $this->_orderObject->getAllItems();
        foreach ($orderItems as $item) {
            $product = $this->_productModel->load($item->getProductId());
            if ($this->helperData->getWeightUnitValue() == '') {
                $errorMessage = "Weight Unit is missing for store";
                $this->returnBackWithError($errorMessage);
            }
            if ($item->getParentItem()) {
                $itemPrice = $item->getParentItem()->getPrice();
            } else {
                $itemPrice = $item->getPrice();
            }
            if ($itemPrice == '') {
                $errorMessage = "price is missing for product - " . $item->getSku();
                $this->returnBackWithError($errorMessage);
            }
            $_commercial['CommercialInvoiceDetail'][$i]['Quantity'] = intval($item->getQtyOrdered());
            $_commercial['CommercialInvoiceDetail'][$i]['UnitType'] = $this->helperData->getWeightUnitValue();
            $_commercial['CommercialInvoiceDetail'][$i]['CountryofManufacture'] = $product->getData('country_of_manufacture') ? $product->getData('country_of_manufacture') : 'SA';
            $_commercial['CommercialInvoiceDetail'][$i]['Description'] = ($item->getDescription() != '') ? $item->getDescription() : '-';
            $_commercial['CommercialInvoiceDetail'][$i]['UnitCost'] = number_format($itemPrice, 3);
            $_commercial['CommercialInvoiceDetail'][$i]['CustomsCommodityCode'] = $product->getData('customscommoditycode') ? $product->getData('customscommoditycode') : $product->getSku();
            $_commercial['CommercialInvoiceDetail'][$i]['Currency'] = $_apiRequiredDataArray['CurrencyCode'];
            $i++;
        }
        return $_commercial;
    }

    /**
     * call Naqel Api
     *
     * @param array $apiData
     *
     * @return array
     */
    public function _callNaqelApi($apiRequestData)
    {
        try {
            $soapClient = $this->helperData->callNaqelSoapApi();
            $response = $soapClient->CreateWaybill($apiRequestData);
            $apiResponseData = json_decode(json_encode($response), true);
            //echo "<pre>";
            //var_dump($apiRequestData);
            // var_dump($apiResponseData);
            // echo "</pre>";
            // die();
            return $this->_saveNaqelApiResponseDB($apiResponseData['CreateWaybillResult']);
        } catch (Exception $e) {
            $this->returnBackWithError($e->getMessage());
        }
    }

    /**
     * save Naqel Api response to database
     *
     * @param array $apiResponseData
     *
     * @return bool
     */
    public function _saveNaqelApiResponseDB($apiResponseData)
    {
        try {
            if ($apiResponseData["HasError"] == 1) {
                $errormessage = "Naqel Client Error :" . $apiResponseData["Message"];
                $this->helperData->naqelLogger($errormessage);
                $this->returnBackWithError($errormessage);
            } else {
                $waybillNo = isset($apiResponseData["WaybillNo"]) ? $apiResponseData["WaybillNo"] : '';
                $this->addShipmentTrackInfo($waybillNo);
                $insertData = array(
                    "entity_id" => $this->_entity_id,
                    "has_error" => isset($apiResponseData["HasError"]) ? $apiResponseData["HasError"] : '',
                    "waybill_no" => isset($apiResponseData["WaybillNo"]) ? $apiResponseData["WaybillNo"] : '',
                    "booking_ref_no" => isset($apiResponseData["BookingRefNo"]) ? $apiResponseData["BookingRefNo"] : '',
                    "waybill_key" => isset($apiResponseData["Key"]) ? $apiResponseData["Key"] : '',
                    "message" => isset($apiResponseData["Message"]) ? $apiResponseData["Message"] : '',
                    "created_at" => date('Y-m-d H:i:s')
                );
                $query_result = $this->_WaybillFactory->create()->setData($insertData)->save();
                if ($query_result) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } catch (Exception $e) {
            $this->returnBackWithError($e->getMessage());
        }
    }

    /**
     * Return back with error message
     *
     * @param string
     * @return null
     */
    public function returnBackWithError($errorMessage)
    {
        $this->helperData->naqelLogger($errorMessage);
        $this->messageManager->addError("Naqel Shipping :- " . substr($errorMessage, 0, 215));
        $redirectUrl = $this->redirect->getRedirectUrl();
        $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        die();
    }

    public function addShipmentTrackInfo($waybillNo)
    {
        $carrier = 'Naqel Shipping';
        $title = 'Naqel Shipping';
        try {
            $track = $this->trackFactory->create()->setTrackNumber($waybillNo)->setCarrierCode($carrier)->setTitle($title);
            $this->shipment->addTrack($track);
            //$this->shipmentRepository->save($shipment);
            //$this->_shipmentNotifier->notify($shipment);
        } catch (NoSuchEntityException $e) {
            //Shipment does not exist
        }
    }
}
