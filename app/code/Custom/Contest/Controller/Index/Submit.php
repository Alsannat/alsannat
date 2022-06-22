<?php
 
namespace Custom\Contest\Controller\Index;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Custom\Contest\Model\ContestFactory;
use Magento\Framework\Controller\ResultFactory;
use Webengage\Event\Helper\Data;
 
class Submit extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $contestfactory;
    protected $resultJsonFactory;
 
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ContestFactory $contestfactory,
		\Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->contestfactory = $contestfactory;
		$this->subscriberFactory= $subscriberFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        try {

            $response = [];
            $data = (array)$this->getRequest()->getPost();
            $email =$this->getRequest()->getPost('email');
            $mobile =$this->getRequest()->getPost('phone_no');
            $model = $this->contestfactory->create();
            
            $check_email = $model->getCollection()->addFieldToFilter('email', $email);
            $check_mobile = $model->getCollection()->addFieldToFilter('phone_no', $mobile);

            if(count($check_email) || count($check_mobile)){
                $response['message'] = __("your email id or mobile number already exists.");
                $response['status'] = false;
            }else{
                if ($data) {
					$this->subscriberFactory->create()->subscribe($email);
                    $model = $this->contestfactory->create();
                    $model->setData($data)->save();
                    $response['message'] = __("You have participated Successfully!");
                    $response['status'] = true;
                }
            }
        } catch (\Exception $e) {
            $response['message'] = __("We can\'t submit your request, Please try again.");
            $response['status'] = false;
        }

        return $this->resultJsonFactory->create()->setData($response);
 
    }
}