<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 29/01/2016
 * Time: 13:13
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\GiftCard\Model\TemplateFactory;
use Magenest\GiftCard\Controller\Adminhtml\Template as TemplateController;

class Delete extends TemplateController
{
    // \Magento\Catalog\Model\Product\Attribute\Source\Status
    protected $_templateFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TemplateFactory $templateFactory
    ) {
        $this->_templateFactory = $templateFactory;
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        $model = $this->_templateFactory->create();

        if ($templateId = $this->getRequest()->getParam('id')) {
            $resultRedirect = $this->resultRedirectFactory->create(ResultFactory::TYPE_REDIRECT);
            try {
                $model->load($templateId)->delete();

                $this->messageManager->addSuccess(__('You deleted the template.'));
                return $resultRedirect->setPath("*/*/");
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addError(__('We can\'t delete this rate because of an incorrect template ID.'));
                return $resultRedirect->setPath("template/*/");
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something went wrong deleting this template.'));
            }

            $resultRedirect->setPath("*/*/");
            return $resultRedirect;
        }
    }
}
