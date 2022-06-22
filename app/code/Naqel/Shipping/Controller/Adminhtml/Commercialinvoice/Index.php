<?php

namespace Naqel\Shipping\Controller\Adminhtml\Commercialinvoice;

use Naqel\Shipping\Model\WaybillFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
ob_start();

class Index extends \Magento\Framework\App\Action\Action
{
  protected $_filesystem;
  protected $_pageFactory;
  protected $_WaybillFactory;
  protected $request;
  protected $helperData;
  protected $messageManager;
  protected $responseFactory;
  protected $redirect;
  /**
   * @param \Magento\Backend\Block\Widget\Context $context
   * @param \Magento\Framework\Message\ManagerInterface $messageManager
   * @param \Magento\Framework\View\Result\PageFactory $pageFactory
   * @param \Magento\Framework\App\Request\Http $request
   * @param \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory
   * @param \Naqel\Shipping\Helper\Data $helperData
   * @param \Magento\Framework\Filesystem $filesystem
   * @param \Magento\Framework\App\Response\RedirectInterface $redirect
   * @param \Magento\Framework\App\ResponseFactory $responseFactory
   * @param array $data
   */
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Framework\View\Result\PageFactory $pageFactory,
    \Magento\Framework\App\Request\Http $request,
    \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory,
    \Naqel\Shipping\Helper\Data $helperData,
    \Magento\Framework\Filesystem $filesystem,
    \Magento\Framework\App\Response\RedirectInterface $redirect,
    \Magento\Framework\App\ResponseFactory $responseFactory
    )
  {
    $this->_pageFactory = $pageFactory;
    $this->messageManager = $messageManager;
    $this->request = $request;
    $this->helperData = $helperData;
    $this->_WaybillFactory = $WaybillFactory;
    $this->_filesystem = $filesystem;
    $this->redirect = $redirect;  
    $this->responseFactory = $responseFactory;
    parent::__construct($context);
  }


  public function execute()
  {
          
         $data = $this->request->getParams();
          if(isset($data['entity_id']) && $data['entity_id'] !='')
          {
              try{

                  $entity_id = $data['entity_id'];

                   $waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData(); 

                  $order_waybill_no  = $waybill_data[0]['waybill_no'];

                  if(isset($order_waybill_no) && $order_waybill_no !='')
                   {

                      $clientInfo = $this->helperData->getNaqelClientInfo();

                      $apiRequestData = array(

                          'clientInfo' => $clientInfo,
                          'InvoiceNo'  =>  $order_waybill_no
                      );      

                      $apiResponse = $this->_callNaqelGetCommercialInvoiceApi($apiRequestData);
                      
                      header("Content-disposition: attachment; filename=Invoice.pdf");
                      header("Content-type: application/pdf");
                      
                      print_r($apiResponse);
                      exit();

                   } 

              }catch(\Exception $e)
              {
                $message = "Naqel Shipping :- " . $e->getMessage();
                $this->returnBackWithError($message);
                die();
              }

          }else
          { 
            $message = "Naqel Shipping :- " . "order id not found for Commercialinvoice generation";
            $this->returnBackWithError($message);
            die();
            
          }
  }


  /**
   * This function will call Naqel Api and return response
   *
   * @param array 
   * @return array
   */
  public function _callNaqelGetCommercialInvoiceApi($apiRequestData)
  {
        try{   

            $soapClient = $this->helperData->callNaqelSoapApi();

            $response = $soapClient->GetCommercialInvoice($apiRequestData);

            return $response->GetCommercialInvoiceResult;

        }catch(\Exception $e)
        {
          $message = "Naqel Shipping :- " . $e->getMessage();
          $this->returnBackWithError($message);
        }
  }

  /**
   * Return back with error message  
   * 
   * @param string
   * @return null
   */
  public function returnBackWithError($message)
  {
          $this->helperData->naqelLogger($message); 
          $this->messageManager->addError($message);
          $redirectUrl = $this->redirect->getRedirectUrl();
          $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
          die();
  }
}