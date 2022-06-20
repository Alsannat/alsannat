<?php

namespace Custom\Contest\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;
	
	protected $_forwardFactory;
	
	/**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\ForwardFactory $forwardFactory,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->_forwardFactory = $forwardFactory;
        parent::__construct($context);
    }
	
    /**
     * Default Contest Index page
     *
     * @return void
     */
    public function execute()
    {
		
		/* Redirect 404 */
		$resultForward = $this->_forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward; 
		/* End Redirect */
		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
		$this->_view->getPage()->getConfig()->getTitle()->set(__('Site Contest'));
        $listBlock = $this->_view->getLayout()->getBlock('contest.list');

        if ($listBlock) {
            $currentPage = abs(intval($this->getRequest()->getParam('p')));
            if ($currentPage < 1) {
                $currentPage = 1;
            }
            
            $listBlock->setCurrentPage($currentPage);
        }
        
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
