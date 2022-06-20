<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Order;

use Naqel\Shipping\Model\WaybillFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order;


class massCommercialinvoice extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction {

    /**
     * @var InvoiceSender
     */
    protected $_invoiceSender;

    /**
     * @var object
     */
    protected $collectionFactory;
    protected $_WaybillFactory;
    protected $helperData;
    protected $_filesystem;

    public function __construct(
        Context $context, 
        Filter $filter, 
        CollectionFactory $collectionFactory,
        InvoiceSender $invoiceSender,
        \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory,
        \Naqel\Shipping\Helper\Data $helperData,
        \Magento\Framework\Filesystem $filesystem    
    ) {
        parent::__construct($context, $filter); 
        $this->collectionFactory = $collectionFactory;
        $this->_invoiceSender = $invoiceSender;    
        $this->_filesystem = $filesystem;
        $this->_WaybillFactory = $WaybillFactory;
        $this->helperData = $helperData;
    }

    //***// //***//
        // This will perform bulk Naqel Commercial Invoice  download in Zip format
    //***// //***//
    protected function massAction(AbstractCollection $collection) 
    {

        
         $files_base_name = array();
         if (!class_exists('\ZipArchive')) 
         {
                
                $message = "ZipArchive class not found";
                $this->helperData->naqelLogger($message);
                $this->messageManager->addError($message);
                $redirectUrl = $this->redirect->getRedirectUrl();
                $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        }

        foreach ($collection->getItems() as $order)
        {
          if($order->getId() !='')
          {
            try{
              $entity_id = $order->getId();

              $waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData();
               if(count($waybill_data) > 0)
               {
                   $order_waybill_no  = $waybill_data[0]['waybill_no'];
                   if(isset($order_waybill_no) && $order_waybill_no !='')
                   {
                      $order_waybill_no;

                      $clientInfo = $this->helperData->getNaqelClientInfo();
                      $apiRequestData = array(

                          'clientInfo' => $clientInfo,
                          'InvoiceNo'  =>  $order_waybill_no
                      );

                      $apiResponse = $this->_callNaqelGetCommercialInvoiceApi($apiRequestData);
                      $filepath = "Naqel/CommercialInvoice/Invoice_".$order_waybill_no.".pdf";
                      $Write_media = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
                      $Write_media->writeFile($filepath,$apiResponse);

                      //$arr[] = $filepath;
                      $files_base_name[] = basename($filepath);

                    } 
                }
                else
                {

                      $message = "Naqel Shipping :- " . "You can't create a Commercial Invoice for order id - ". $entity_id. ", (don't have waybill no.)";
                      $this->messageManager->addError(__('Cannot create Commercial Invoice for order %1 (Waybill no. not found)', $order->getIncrementId()));
                      $this->logErrorToFile($message);
                      

                }  

              }catch(\Exception $e)
              {
                $message = "Naqel Shipping :- " . $e->getMessage();
                $this->logErrorToFile($message);
                die();
              }

          }else
          {
            $message = "Naqel Shipping :- " . "order not found!";
            $this->messageManager->addError(__('order not found'));
            $this->logErrorToFile($message);
          }
        }
        /*
        print_r($files_base_name);
        die();*/
        if(count($files_base_name) > 0)
        {

            $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        
            $ZipPath = $mediapath."Naqel/CommercialInvoice";
            
            chdir($ZipPath); 
            
            $zip = new \ZipArchive();
            $zip->open('CommercialInvoice.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            $file_names = $files_base_name;
            $file_path = $mediapath.'Naqel/CommercialInvoice/CommercialInvoice.zip';

            foreach($file_names as $key =>$file)
            { //add pdf file to zip file
                 $zip->addFile($file);
            }

            $zip->close();
              
            foreach($file_names as $key =>$file)
            {
              //delete generated pdf files
              unlink($file);
            }
            //$this->messageManager->addSuccess(__('Commercial Commercial Invoice generated!'));
            header("Content-disposition: attachment; filename=CommercialInvoice.zip");
            header("Content-type: application/zip");
            @readfile($file_path);
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->getComponentRefererUrl());
            return $resultRedirect;
              
        }else
        {
            $message = "Naqel Shipping :- " . "No Commercial Invoice found!";
            $this->messageManager->addError(__('No Commercial Invoice found!'));
            $this->logErrorToFile($message);
              
        }
        

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->getComponentRefererUrl());
        return $resultRedirect;
    }

    public function _callNaqelGetCommercialInvoiceApi($apiRequestData)
    {

        try{   

            $soapClient = $this->helperData->callNaqelSoapApi();

            $response = $soapClient->GetCommercialInvoice($apiRequestData);

            return $response->GetCommercialInvoiceResult;

        }catch(\Exception $e)
        {
          $message = "Naqel Shipping :- " . $e->getMessage();
          $this->messageManager->addError(__($message));
          $this->logErrorToFile($message);
        }
    }


    public function logErrorToFile($message)
   {
          $this->helperData->naqelLogger($message);
   }
}