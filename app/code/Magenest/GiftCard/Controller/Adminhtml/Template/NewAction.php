<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 28/01/2016
 * Time: 13:14
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magenest\GiftCard\Controller\Adminhtml\Template as TemplateController;

class NewAction extends TemplateController
{
    protected $_resultForwardFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
