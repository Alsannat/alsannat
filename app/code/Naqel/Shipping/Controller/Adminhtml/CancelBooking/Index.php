<?php
/**
* @category   Naqel
* @package    Naqel_Shipping
*/
namespace Naqel\Shipping\Controller\Adminhtml\CancelBooking;

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
        $entity_id  = $this->request->getParam('entity_id');
        if(isset($entity_id) && $entity_id !='') {
            $waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData(); 
            $ClientInfo = $this->helperData->getNaqelClientInfo();
            $apiRequestData = [
                'ClientInfo' => $ClientInfo,
                'RefNo'  => $waybill_data[0]['booking_ref_no'], // $bookingRef,
                'BookingKey'  =>  '0', //$waybill_data[0]['waybill_key']
                'CancelReason'  =>  'Cancel Booking'
            ];            
            $soapClient = $this->helperData->callNaqelSoapApi();
			$response = $soapClient->CancelBooking($apiRequestData);			
            $result = $this->jsonResultFactory->create();
            $result->setData($response->CancelBookingResult);
            return $result;
        }
    }
}
