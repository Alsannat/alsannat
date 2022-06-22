<?php

namespace Lotus\SMSAShipment\Plugin;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\ObjectManagerInterface;

class PluginBeforeView
{
    protected $object_manager;
    protected $_backendUrl;
    protected $helper;

    public function __construct(
        ObjectManagerInterface $om,
        UrlInterface $backendUrl,
        \Lotus\SMSAShipment\Helper\Data $helper
    ) {
        $this->object_manager = $om;
        $this->_backendUrl = $backendUrl;
        $this->helper = $helper;
    }

    public function beforeGetOrderId(\Magento\Sales\Block\Adminhtml\Order\View $view){
$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/hn_beforeGetOrderId.log');
$logger = new \Zend\Log\Logger();
$logger->addWriter($writer);
$logger->info('Your text message');
        $order_id = $view->getOrder()->getId();
        if($order_id){
            $this->helper->getSMSAShipmentsStatus($order_id);
        }
        $createShipment = $this->_backendUrl->getUrl('ltshipbysmsa/shipment/create',['order_id' => $order_id]);
        $sendTrack = $this->_backendUrl->getUrl('ltshipbysmsa/shipment/trackemail',['order_id' => $order_id]);
        $sendTrackSms = $this->_backendUrl->getUrl('ltshipbysmsa/shipment/tracksms',['order_id' => $order_id]);
        $view->addButton(
                'smsaship',
                ['label' => __('SMSA Ship'), 'onclick' => 'setLocation(\'' . $createShipment . '\')', 'class' => 'smsaship-tbn'],
                -1
            );

        if($this->helper->getTrackEmail()){
            $view->addButton(
                'smsatrackemail',
                ['label' => __('Send SMSA Email'), 'onclick' => 'setLocation(\'' . $sendTrack . '\')', 'class' => 'smsatrack-email'],
                -1
            );
            $view->addButton(
                'smsatracksms',
                ['label' => __('Send SMSA SMS'), 'onclick' => 'setLocation(\'' . $sendTrackSms . '\')', 'class' => 'smsatrack-sms'],
                -1
            );
        }

        return null;
    }

}