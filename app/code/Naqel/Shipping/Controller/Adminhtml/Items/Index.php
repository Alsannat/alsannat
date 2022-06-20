<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Items;

class Index extends \Naqel\Shipping\Controller\Adminhtml\Items
{
    /**
     * Items list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Naqel_Shipping::test');
        $resultPage->getConfig()->getTitle()->prepend(__('Naqel Shipping Cities'));
        $resultPage->addBreadcrumb(__('Naqel'), __('Naqel'));
        $resultPage->addBreadcrumb(__('City'), __('City'));
        return $resultPage;
    }
}