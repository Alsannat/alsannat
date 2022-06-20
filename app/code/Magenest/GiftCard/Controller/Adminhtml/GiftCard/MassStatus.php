<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 23/08/2016
 * Time: 11:09
 */

namespace Magenest\GiftCard\Controller\Adminhtml\GiftCard;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magenest\GiftCard\Model\GiftCardFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassStatus extends \Magento\Backend\App\Action
{
    protected $_filter;

    protected $giftcardFactory;

    /**
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param WarehousesFactory $warehousesFactory
     * @param Registry $registry
     * @param LoggerInterface $loggerInterface
     * @param Filter $filter
     */
    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        GiftCardFactory $giftCardFactory,
        Registry $registry,
        LoggerInterface $loggerInterface,
        Filter $filter
    ) {
        $this->_filter = $filter;
        $this->giftcardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $status = (int) $this->getRequest()->getParam('status');
        $collection = $this->_filter->getCollection($this->giftcardFactory->create()->getCollection());
        $total = 0;

        try {
            foreach ($collection as $item) {
                $item->setData('status', $status)->save();
                $total++;
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $total));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('giftcard/*/index');
    }
}
