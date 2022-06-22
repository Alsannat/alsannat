<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/08/2016
 * Time: 10:54
 */
namespace Magenest\GiftCard\Controller\Adminhtml\GiftCard;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
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
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $count = 0;
        foreach ($collection->getItems() as $item) {
            $item->delete();
            $count++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $count)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('giftcard/*/index');
    }
}
