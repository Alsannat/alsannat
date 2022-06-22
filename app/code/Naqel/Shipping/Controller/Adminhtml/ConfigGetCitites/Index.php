<?php
/**
* @category   Naqel
* @package    Naqel_Shipping
*/
namespace Naqel\Shipping\Controller\Adminhtml\ConfigGetCitites;

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
        $countryCode =  $this->request->getParam('countryCode');
        if(isset($countryCode) && $countryCode !='') {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $connection = $objectManager->get('Magento\Framework\App\ResourceConnection')->getConnection('\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION'); 
            $cityData = $connection->fetchAll("SELECT client_city_name, code, country_code FROM naqel_shipping_naqel_city WHERE country_code = '" . $countryCode . "'");
            $result = $this->jsonResultFactory->create();
            $result->setData($cityData);
            return $result;
        }
    }

}
