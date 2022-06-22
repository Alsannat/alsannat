<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 21:23
 */
namespace Magenest\GiftCard\Controller\Adminhtml\Template;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\GiftCard\Model\ResourceModel\Template\CollectionFactory;

class MassDelete extends \Magenest\GiftCard\Controller\Adminhtml\Template
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
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $templateDeleted = 0;
        foreach ($collection->getItems() as $template) {
            $template->delete();
            $templateDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $templateDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('giftcard/*/index');
    }
}
