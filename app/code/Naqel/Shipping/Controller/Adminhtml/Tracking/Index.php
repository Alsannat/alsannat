<?php
/**
* @category   Naqel
* @package    Naqel_Shipping
*/
namespace Naqel\Shipping\Controller\Adminhtml\Tracking;

use Naqel\Shipping\Model\WaybillFactory;

ob_start();

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_WaybillFactory;
    protected $request;
    protected $helperData;

    /**
    * @param \Magento\Backend\Block\Widget\Context $context
    * @param \Magento\Framework\View\Result\PageFactory $pageFactory
    * @param \Magento\Framework\App\Request\Http $request
    * @param \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory
    * @param \Naqel\Shipping\Helper\Data $helperData
    */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Request\Http $request,
        \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory,
        \Naqel\Shipping\Helper\Data $helperData
    ) {
        $this->_pageFactory = $pageFactory;
        $this->request = $request;
        $this->helperData = $helperData;
        $this->_WaybillFactory = $WaybillFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data['entity_id'] =  $this->request->getParam('entity_id');
        if(isset($data['entity_id']) && $data['entity_id'] !='') {
            try {
                $entity_id = $data['entity_id'];
                $waybill_data = $this->_WaybillFactory->create()->getCollection ()->addFieldToFilter('entity_id', array('eq' => $entity_id))->getData(); 
                $order_waybill_no  = $waybill_data[0]['waybill_no'];

                if(isset($order_waybill_no) && $order_waybill_no !='') {

                    $ClientInfo = $this->helperData->getNaqelClientInfo();
                    $apiRequestData = [
                        'ClientInfo' => $ClientInfo,
                        'WaybillNo'  =>  $order_waybill_no
                    ];

                    $ajaxResponse = "<table class='table data-grid table-bordered track_tbl'>";
                    $ajaxResponse .= "<thead><tr>
                        <th></th>
                        <th>S.NO</th>
                        <th>STATUS</th>
                        <th>SHIPPING</th>
                        <th>DATE/TIME</th>
                        </tr></thead>";
                    $s_no=1;
                    $ajaxResponse .="<tbody>";
                    $apiResponseRes = $this->_callNaqelApi($apiRequestData);

                    if(!isset($apiResponseRes[0])) {
                        $apiResponse[] = $apiResponseRes;
                    } else {
                        $apiResponse = $apiResponseRes;
                    }
                    if(count($apiResponse[0]) > 0) {
                        usort($apiResponse, ["Naqel\Shipping\Controller\Adminhtml\Tracking\Index", "sortApiResponseByDate"]);
                        foreach ($apiResponse as $key => $value) {
                            $ajaxResponse .= "<tr>";
                            $ajaxResponse .= "<td class='track_dot'><span class='track_line'></span></td>";
                            $ajaxResponse .= "<td>".$s_no++."</td>";
                            $ajaxResponse .= "<td>".$value['Activity']."</td>";
                            $ajaxResponse .= "<td>Naqel express</td>";
                            $ajaxResponse .= "<td>".date('j M Y g:i A',strtotime($value['Date']))."</td>";
                            $ajaxResponse .= "</tr>";
                        }
                    } else {
                        $ajaxResponse .= "<tr><td style='text-align:center;' colspan='5'>No record found!</td></tr>";
                    } 

                    $ajaxResponse .= "</tbody></table>";
                    echo $ajaxResponse;
                    exit();
                }
            } catch(\Exception $e) {
                $this->helperData->naqelLogger($e->getMessage());
                die('something went wrong try after some time');
            }
        } else {
            $this->helperData->naqelLogger('order_id value not found!');
            die('order_id value required');
        }
    }

    /**
    * @param array 
    */
    public function sortApiResponseByDate($A, $B)
    {
        //sort api response by date asc. order 
        try {
            $T1 = strtotime($A['Date']);
            $T2 = strtotime($B['Date']);
            return $T1 - $T2;

        } catch(\Exception $e) {
            $this->helperData->naqelLogger($e->getMessage());
            die('something went wrong try after some time');
        }
    }

    /**
    * this function will call Naqel Api and return response
    *
    * @param array 
    * @return array
    */
    public function _callNaqelApi($apiRequestData)
    {
        try {
            $soapClient = $this->helperData->callNaqelSoapApi();

            $response = $soapClient->TraceByWaybillNo($apiRequestData);

            $apiResponseData = json_decode(json_encode($response),true);

            return $apiResponseData['TraceByWaybillNoResult']['Tracking'] ?? [];
        } catch(\Exception $e) {
            $this->helperData->naqelLogger($e->getMessage());
            die($e->getMessage());
        }
    }
}
