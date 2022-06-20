<?php

namespace MGS\Popup\Block\Adminhtml\Popup\Grid\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Store\Model\StoreManagerInterface;

class Image extends AbstractRenderer
{
    private $_storeManager;

    public function __construct(\Magento\Backend\Block\Context $context, StoreManagerInterface $storemanager, array $data = [])
    {
        $this->_storeManager = $storemanager;
        parent::__construct($context, $data);
        $this->_authorization = $context->getAuthorization();
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if ($this->_getValue($row) != '') {
            $imageUrl = $mediaDirectory . $this->_getValue($row);
            return '<img src="' . $imageUrl . '" style="max-width: 200px;"/>';
        } else {
            return '';
        }
    }
}
