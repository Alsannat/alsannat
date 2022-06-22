<?php
/**
* @category   Naqel
* @package    Naqel_Shipping
*/
namespace Naqel\Shipping\Controller\Adminhtml\Lastevent;

use Naqel\Shipping\Model\WaybillFactory;

ob_start();

class Index extends \Magento\Backend\App\Action
{
    protected $_pageFactory;
    protected $_WaybillFactory;
    protected $request;
    protected $helperData;
    protected $jsonResultFactory;


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
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        $this->helperData = $helperData;
        $this->_WaybillFactory = $WaybillFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        parent::__construct($context);
    }

    public function execute() {
        $waybill_no =  $this->request->getParam('waybill_no');
        if(isset($waybill_no) && $waybill_no !='') {
            $ClientInfo = $this->helperData->getNaqelClientInfo();
            $apiRequestData = [
                '_clientInfo' => $ClientInfo,
                'WaybillNoList'  => array("int" => $waybill_no),
            ];
            //$apiResponseRes = $this->callNaqelApi($apiRequestData);
            $soapClient = $this->helperData->callNaqelSoapApi();
			$response = $soapClient->LastEventCode($apiRequestData);
			try {
				$message = $response->LastEventCodeResult->LastEventTrackingStatus->Activity;
			} catch (Exception $e) {
				$message = "Error in getting the Naqel Shipping LastEventResult.";
			}			
			//$response->LastEventCodeResult;
            $result = $this->jsonResultFactory->create();
            $result->setData($response->LastEventCodeResult);
            return $result;
        }
    }

}
