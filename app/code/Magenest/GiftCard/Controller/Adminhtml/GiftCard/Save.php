<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 11:42
 */

namespace Magenest\GiftCard\Controller\Adminhtml\GiftCard;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class Save extends Action
{

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $params = $this->getRequest()->getParams();
        $id = $params['id'];

        try {
            $giftcardsEditableFields = [
                'date_expired' => '',
                'sender_name' => '',
                'sender_email' => '' ,
                'recipient_name' => '',
                'headline' =>'' ,
                'recipient_email' => '',
                 'message' =>''
            ];

            foreach ($giftcardsEditableFields as $key => $value) {
                if (isset($params[$key])) {
                    $giftcardsEditableFields[$key]= $params[$key];
                }
            }

            //need to format the date

            $expiryDate = new \DateTime(strtotime($params['date_expired']));
            $giftcardsEditableFields['date_expired']= $expiryDate->format('Y-m-d H:i:s');
            $giftcardsEditableFields['id']=$id;
            $model = $this->_objectManager->create("Magenest\GiftCard\Model\GiftCard")->load($id)->addData($giftcardsEditableFields);

            $model->save();

            $this->messageManager->addSuccess(__('You have saved the gift card successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this gift card.'));
        }

        $resultRedirect->setPath('giftcard/giftcard/index');
        return $resultRedirect;
    }
}
