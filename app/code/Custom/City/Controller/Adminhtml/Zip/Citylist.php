<?php
namespace Custom\City\Controller\Adminhtml\Zip;

class Citylist extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_cityCollection;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Custom\City\Model\CityFactory $cityFactory
     * @param \Custom\City\Model\Resource\City\Collection $cityCollection
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Custom\City\Model\CityFactory $cityFactory,
        \Custom\City\Model\Resource\City\Collection $cityCollection,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_cityFactory = $cityFactory;
        $this->_cityCollection = $cityCollection;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
    /**
     *
     *
     * @return void
     */
    public function execute()
    {
        $stateId = $this->getRequest()->getParam('state');
        $city = "<option value=''>".__('--Please Select--')."</option>";
        if ($stateId != '') {
            $citiesArray = $this->_cityCollection->addFieldToFilter('state_id',$stateId);
            foreach ($citiesArray as $_city) {
                if($_city['id']){
                    $value = $_city['id'];
                    $city .= "<option value='".$value."'>" . __($_city['city']) . "</option>";
                }
            }
        }elseif($stateId == '' && $this->getRequest()->getParam('country')!=""){
            $citiesArray = $this->_cityCollection
                ->addFieldToFilter('country_id',$this->getRequest()->getParam('country'))
                ->addFieldToFilter('state_id',0);
            foreach ($citiesArray as $_city) {
                if($_city['id']){
                    $value = $_city['id'];
                    $city .= "<option value='".$value."'>" . __($_city['city']) . "</option>";
                }
            }
        }
        $result['htmlconent']=$city;
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

}