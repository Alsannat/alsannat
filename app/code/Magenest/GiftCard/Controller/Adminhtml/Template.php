<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 26/01/2016
 * Time: 22:58
 */
namespace Magenest\GiftCard\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

abstract class Template extends Action
{
    protected $_templateFactory;

    protected $_collectionFactory;

    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_GiftCard::manage_template')
            ->addBreadcrumb(__('Template'), __('Template'));
        $resultPage->getConfig()->getTitle()->set(__('Gift Card'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_GiftCard::manage_template');
    }
}
