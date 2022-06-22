<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 23:31
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magenest\GiftCard\Model\TemplateFactory;
use Magento\Framework\View\Result\PageFactory;
use Magenest\GiftCard\Controller\Adminhtml\Template as TemplateController;

class Edit extends TemplateController
{
    protected $resultForwardFactory;

    protected $_templateFactory;

    protected $coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TemplateFactory $templateFactory,
        PageFactory $resultPageFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_templateFactory = $templateFactory;
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_templateFactory->create();
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This template no longer exists.'));

                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        $this->coreRegistry->register('giftcard_template', $model);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Template') : __('New Template'));

        return $resultPage;
    }
}
