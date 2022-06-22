<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 08:41
 */

namespace Magenest\GiftCard\Controller\Adminhtml\GiftCard;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Psr\Log\LoggerInterface;

class Edit extends Action
{
    protected $_coreRegistry;

    protected $_logger;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        LoggerInterface $loggerInterface
    ) {

        $this->_coreRegistry = $coreRegistry;
        $this->_logger = $loggerInterface;
        // $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Magenest\GiftCard\Model\GiftCard');
        if ($id) {
            $model->load($id);
            if (!$model->getGiftcardId()) {
                $this->messageManager->addError(__('This giftcard no longer exists.'));
                $this->_redirect('/**');
                return;
            }
        }

        $this->_coreRegistry->register('current_giftcard', $id);
        $this->_coreRegistry->register('id', $model);
        $this->getGiftCardValues($model)  ;
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getGiftcardId() ? $model->getCode() : __('New Gift Card')
        );
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->getConfig()->getTitle()->prepend(__('Gift Card'));
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Gift Card'));
        return $resultPage;
    }

    /**
     * prepare values for form
     * @param $data array
     */
    protected function getGiftCardValues($model)
    {
        $data = $model->getData();

        ($data['status'] == 0) ? $data['status'] =__('Unavailable') : $data['status'] = __('Available') ;

        /** @var  $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem  = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($data['order_item_id']);
        $orderId = $orderItem->getOrderId();
        $data['order_id'] = $orderId;
        $data['order_link'] = $this->getUrl('admin/edit/id/');
        $productName = $orderItem->getProduct()->getName();
        $productUrl = $orderItem->getProduct()->getProductUrl();

        $data['product_name'] = $productName;
        $data['id'] = $model->getId();
        $data['product_link'] = $productUrl;
        $this->_coreRegistry->register('current_giftcard_data', $data);
    }
}
