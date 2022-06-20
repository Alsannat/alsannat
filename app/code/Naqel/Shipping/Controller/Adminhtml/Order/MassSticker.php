<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Api\OrderManagementInterface;

class MassSticker extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{

    protected $orderManagement;
	  protected $helperData;
    protected $_WaybillFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        OrderManagementInterface $orderManagement,
        \Magento\Backend\Model\Auth\Session $authSession,
		    \Naqel\Shipping\Helper\Data $helperData,
        \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory
    ) {
        parent::__construct($context, $filter);
        $this->authSession = $authSession;
        $this->collectionFactory = $collectionFactory;
        $this->orderManagement = $orderManagement;
		    $this->helperData = $helperData;
        $this->_WaybillFactory = $WaybillFactory;
    }

    //***// //***//
        // This will perform mass shipment and SalesOrderShipmentBefore observer will also be called for every order to register order data into Naqel API and store Waybill_no into database table naqel_shipping_waybill_record 
    //***// //***//
    protected function massAction(AbstractCollection $collection)
    {        
        //$model = $this->_objectManager->create('Magento\Sales\Model\Order');
        //$username = $this->authSession->getUser()->getUsername();       
		$processed_ids = array();
        foreach ($collection->getItems() as $order) {
            if (!$order->getEntityId()) {
                continue;
            }
			$entity_id = $order->getId();
			$waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData();
			if(count($waybill_data) > 0)
			{
			   $order_waybill_no  = $waybill_data[0]['waybill_no'];
			   $processed_ids[] = $order_waybill_no;			   
			}
        }
        
		if (!empty($processed_ids)) {			
			$this->getMultiWaybillStickerApi($processed_ids);
		}else{
            $message = "Naqel Shipping :- " . "No Waybill Sticker found!";
            $this->messageManager->addError(__('No Waybill Sticker found!'));
            $this->logErrorToFile($message);
              
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }
	
	public function getMultiWaybillStickerApi($processed_ids)
	{
		$clientInfo = $this->helperData->getNaqelClientInfo();
		$sticker = 'FourMSixthInches'; 
		$apiRequestData = array(
			'clientInfo' => $clientInfo,
			'WaybillNumbers'  =>  $processed_ids,
			'StickerSize' => $sticker
		);		
		try {
			$soapClient = $this->helperData->callNaqelSoapApi();
			$response = $soapClient->GetMultiWaybillSticker($apiRequestData);
      if(!$response->GetMultiWaybillStickerResult->HasError) {
      $this->messageManager->addSuccess(__('%1 order(s) Bulk Stickers are created successfully.', count($processed_ids)));
  			$apiResponse = $response->GetMultiWaybillStickerResult->StickerByte;		        
        header("Content-disposition: attachment; filename=sticker.pdf");
        header("Content-type: application/pdf");
        print_r($apiResponse);
        exit();	
      }else {
        $this->returnBackWithError($response->GetMultiWaybillStickerResult->Message);
      }
		} catch (Exception $e) {
			$message = "Naqel Shipping :- " . $e->getMessage();
			setcookie("naqel_server_response", $message);
			setcookie("naqel_server_response_status", "error");
			$this->returnBackWithError($message);
		}
	}
	
	
	public function returnBackWithError($message) {
		$this->helperData->naqelLogger($message);
		$this->messageManager->addError($message);
		//$redirectUrl = $this->redirect->getRedirectUrl();
		//$this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
		//die();
	}
}
