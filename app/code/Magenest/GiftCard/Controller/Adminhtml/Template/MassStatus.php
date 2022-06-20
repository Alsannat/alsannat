<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 06/09/2017
 * Time: 10:33
 */

namespace Magenest\GiftCard\Controller\Adminhtml\Template;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\GiftCard\Model\ResourceModel\Template\CollectionFactory;

class MassStatus extends \Magenest\GiftCard\Controller\Adminhtml\Template
{
    protected $_filter;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        PageFactory $resultPageFactory
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $resultPageFactory);
    }

    public function execute()
    {
        $status = $this->getRequest()->getParam('status');
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $templateDeleted = 0;
        foreach ($collection->getItems() as $template) {
            $template->setStatus($status);
            $template->save();
            $templateDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been changed status.', $templateDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('giftcard/*/index');
    }
}