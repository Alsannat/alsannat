<?php
namespace Lotus\ChangeOrderStatus\Observer;

use Magento\Framework\Event\ObserverInterface;

class NewCreditmemo implements ObserverInterface
{
    /**
     * @var \Lotus\ChangeOrderStatus\Helper\Data
     */
    protected $helper;
    
    
    /**
     * @param \Lotus\ChangeOrderStatus\Helper\Data $helper
     */
    public function __construct(
        \Lotus\ChangeOrderStatus\Helper\Data $helper
    ){
        $this->helper = $helper;
    }
    
    /**
     * Vendor Save After
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return self
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemo    = $observer->getCreditmemo();
        $order = $creditmemo->getOrder();

        $this->helper->check($order);
        $order->save();
    }
}
