<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


namespace Amasty\Oaction\Controller\Adminhtml\Massaction;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Index extends \Amasty\Oaction\Controller\Adminhtml\Massaction
{
    public function massAction(AbstractCollection $collection)
    {
        $action = $this->getRequest()->getParam('type');
        $param = (int) $this->getRequest()->getParam('notify');

        if ($action == 'status') {
            $param = $this->getRequest()->getParam('status');
        }

        $oaction = $this->getRequest()->getParam('oaction');

        if ($oaction == null) {
            $oaction = [];
        }

        try {
            $className = 'Amasty\Oaction\Model\Command\\'  . ucfirst($action);
            $command = $this->_objectManager->create($className);
            $success = $command->execute($collection, $param, $oaction);

            if ($success) {
                //for combined actions show both messages
                $messages = explode('||', $success);

                foreach ($messages as $message) {
                    $this->messageManager->addSuccessMessage($message);
                }
            }

            // show non critical erroes to the user
            foreach ($command->getErrors() as $errorMessage) {
                $this->messageManager->addErrorMessage($errorMessage);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong.'));
        }

        if ($command && $command->hasResponse()) {
            return $this->_fileFactory->create(
                $command->getResponseName(),
                $command->getResponseBody(),
                DirectoryList::VAR_DIR,
                $command->getResponseType()
            );
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/');

            return $resultRedirect;
        }
    }
}
