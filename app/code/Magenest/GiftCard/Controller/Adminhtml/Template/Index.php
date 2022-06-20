<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 20:47
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magenest\GiftCard\Controller\Adminhtml\Template as TemplateController;

class Index extends TemplateController
{
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Gift Card Template Manager'));

        return $resultPage;
    }
}
