<?php
namespace Custom\City\Controller\Adminhtml\City;

class Regionlist extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    )
    {
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $countryCode = $this->getRequest()->getParam('country');
        $state = "<option value=''>".__('--Please Select--')."</option>";
        $counter = 0;
        if ($countryCode != '') {
            $stateArray =$this->_countryFactory->create()->setId(
                $countryCode
            )->getLoadedRegionCollection()->toOptionArray();
            foreach ($stateArray as $_state) {
                if($_state['value']){
                    $value = $_state['value'];
                    $state .= "<option value='".$value."'>" . __($_state['label']) . "</option>";
                    $counter++;
                }
            }
        }
        if($counter==0){
            $state = "<option value=''>".__('No State')."</option>";
        }
        $result['htmlconent']= $state;
        $result['counter']= $counter;
        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

}