<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Bulkcity;
class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) 
    {
        
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Naqel\Shipping::Bulkcity');
        $resultPage->getConfig()->getTitle()->prepend(__('Upload City'));
        return $resultPage;
    }
}
