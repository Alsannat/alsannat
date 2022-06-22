<?php

namespace Naqel\Shipping\Controller\Index;
use Naqel\Shipping\Model\ShippingFactory;

ob_start();

class GetCity extends \Magento\Framework\App\Action\Action
{
  protected $_pageFactory;
  protected $_shipping;
  protected $request;
  protected $helperData;

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $pageFactory,
    \Magento\Framework\App\Request\Http $request,
    \Naqel\Shipping\Model\ShippingFactory  $shippingFactory,
    \Naqel\Shipping\Helper\Data $helperData
    )
  {
    $this->_pageFactory = $pageFactory;
    $this->request = $request;
    $this->helperData = $helperData;
    $this->_shipping = $shippingFactory;
    parent::__construct($context);
  }

  public function execute(){
      $data = $this->getRequest()->getPostValue();
      
      if($data['country_id'] != ""){
        if($data['country_id'] == "SA"){
          $country_code = "KSA";
        }
        else{
          $country_code = $data['country_id'];
        }
        $city_value = isset($data['city']) ? $data['city']:'';

        $collection = $this->_shipping->create()->getCollection()
        ->addFieldToFilter('country_code', array('eq'=>$country_code));
        $data=$collection->getData();
        if(count($data) > 0)
        {
          
          $html = "<select id='city_select' name='city'><option value=''> </option>";
          foreach ($collection->getData() as $key => $value) {
           $selected = ($city_value==$value['code'])?'selected':'';
           $html.="<option value=".$value['code']." ".$selected." >".$value['city_name']."</option>";
          }
          $html . "</select>";
          echo $html;  
        }else{
          echo 0;
        }
        
      }
  }

    
}