<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Bulkcity;

 use Magento\Framework\App\Action\Action;
 use Magento\Framework\App\Action\Context;
 use Magento\Framework\View\Result\PageFactory;

class Save extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $_shippingFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Naqel\Shipping\Model\ShippingFactory $shippingFactory,
     * @param \Magento\Framework\File\Csv $csv
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Naqel\Shipping\Model\ShippingFactory $shippingFactory,
        \Magento\Framework\File\Csv $csv
    ) 
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_shippingFactory = $shippingFactory;
        $this->csv = $csv;
        parent::__construct($context);
    }
    
    public function execute()
    {       
        if ($_FILES['fileupload']['name'] =='')
        {
            $this->messageManager->addSuccess(__('Invalid file upload attempt.'));
            $this->_redirect('naqel_shipping/*/index');
            return;
        }
         
         $extension = pathinfo($_FILES["fileupload"]["name"], PATHINFO_EXTENSION);
        if ($extension == "csv")
        {
            try {
            //select old city data in database for avoiding duplicacy
            $cityFactory = $this->_shippingFactory->create();
            $collection = $cityFactory->getCollection();
            $uploded_city_code = array();
            foreach($collection as $item){
                $uploded_city_code[] =$item->getCode();
            }
                
            $csvData = $this->csv->getData($_FILES['fileupload']['tmp_name']);
            $insert = array();
            $alreadyExistCount = 0;
            $inSufficientDataCount  = 0;
            $uploadCount = 0;
            foreach ($csvData as $row => $data) {
                //skip 1 row
                if($row > 0)
                {
                    if ((count($data) >= 4))
                    {
                        if(!in_array($data[1], $uploded_city_code))
                        {
                            //first 4 data is required so count > 4 is set in if condition
                            $insert['code'] = $data[0];
                            $insert['city_name'] = $data[1];
                            $insert['client_city_name'] = $data[1];
                            $insert['client_city_name_ar'] = $data[2];
                            $insert['country_code'] = $data[4];
                            $insert['client_country_name'] = $this->getCountryName($data[4]);
                            
                            $insert['status'] = 1;
                            $insert['created_at'] = date('Y-m-d H:i:s');
//
//                            $insert['oda'] = $data[4];
//                            $insert['city_longitude'] = $data[5];
//                            $insert['city_lattitude'] = $data[6];

                            $this->_shippingFactory->create()->setData($insert)->save();  
                            $uploadCount++;   
                        }else{
                            $alreadyExistCount++;    
                        }
                    }else{
                        $inSufficientDataCount++;
                    }    
                }
                
            }   
            // set error for already exist
            if($alreadyExistCount > 0){
                $this->messageManager->addError(__($alreadyExistCount .' city data already exist!'));    
            }
            // set error for insufficient data for city 
            if($inSufficientDataCount > 0){
                $this->messageManager->addError(__($inSufficientDataCount . ' City data was insufficient data to Insert!.( code, city_name, country_code, oda are required!)'));    
            }
            if($uploadCount > 0 ){
                $this->messageManager->addSuccess(__($uploadCount . ' city data uploded successfully.'));
                $this->_redirect('naqel_shipping/items');
                return;    
            }else
            {
                 $this->_redirect($this->_redirect->getRefererUrl());
                return;    
            }
            
            

            } catch (Exception $e) {

                $this->messageManager->addSuccess(__('Something went wrong!.'));
                $this->_redirect('naqel_shipping/items');
                return;
            }
            
        } 
        else{
            $this->messageManager->addError(__('Invalid file type. File type must be CSV!'));
            $this->_redirect('naqel_shipping/*/index');
            return;
        }
    }
    
    public function getCountryName($code){
        $countryName = '';
        switch ($code) {
            case 'KSA':
                $countryName = 'Saudi Arabia';
                break;
            case 'AE':
                $countryName = 'UAE';
                break;
            case 'LB':
                $countryName = 'Lebanon';
                break;
            case 'BH':
                $countryName = 'Bahrain';
                break;
            case 'JO':
                $countryName = 'Jordan';
                break;
        }
        return $countryName;
    }
}
