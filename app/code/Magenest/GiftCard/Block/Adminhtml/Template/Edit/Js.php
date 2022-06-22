<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 28/01/2016
 * Time: 21:34
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Edit;

use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\Registry;

class Js extends \Magento\Backend\Block\Template
{
    protected $jsonHelper;

    protected $_logger;

    protected $_templateFactory;

    protected $_coreRegistry;

    public function __construct(
        Registry $coreRegistry,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        array $data
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_templateFactory = $templateFactory;
        $this->_logger = $context->getLogger();
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    public function getCenterImage()
    {
        $image = $this->getViewFileUrl('Magenest_GiftCard::images/center.jpg');
        return $this->jsonHelper->jsonEncode($image);
    }

    public function getLeftImage()
    {
        $image = $this->getViewFileUrl('Magenest_GiftCard::images/left.jpg');
        return $this->jsonHelper->jsonEncode($image);
    }

    public function getRightImage()
    {
        $image = $this->getViewFileUrl('Magenest_GiftCard::images/right.jpg');
        return $this->jsonHelper->jsonEncode($image);
    }

    public function getPreviewUrl()
    {
        return $this->getUrl('giftcard/template/preview');
    }

    public function getTemplateData()
    {
        $model = $this->_coreRegistry->registry('giftcard_template');
        $id = $model->getId();

        if ($id != null) {
            $data = $this->_templateFactory->create()->load($id)->getData();

            $coordinates = unserialize($data['extra_field']);

            return $coordinates;
        } else {
            return [];
        }
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getUploadUrl()
    {
        return $this->getUrl('giftcard/template/uploader');

    }
    public function getBackGroundUploadUrl()
    {
        return $this->getUrl('giftcard/template/backgrounduploader');

    }
}
