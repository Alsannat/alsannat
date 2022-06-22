<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 02/02/2016
 * Time: 14:48
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;
use Psr\Log\LoggerInterface as Logger;

class Image extends Generic implements TabInterface
{
    protected $_storeManager;

    protected $_templateFactory;

    public function __construct(
        \Magenest\GiftCard\Model\TemplateFactory $templateFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_storeManager = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected $_template = 'template/image.phtml';

    public function getTabLabel()
    {
        return __('Main Image Uploads');
    }

    public function getTabTitle()
    {
        return __('Main Image Uploads');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    public function getMediaUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    public function getSavedMainImage()
    {
        $template = $this->_coreRegistry->registry('giftcard_template');
        $model = $this->_templateFactory->create();
        $data = $model->load($template->getId())->getData();

        if (!empty($data)) {
            $end_saved_image_selected = '';
            $saved_image_selected = $data['saved_image_selected'];

            if ($saved_image_selected) {
                $serSelectedImg = explode('/' , $saved_image_selected);
                $end_saved_image_selected = end($serSelectedImg);

            }

            $main_image = unserialize($data['main_image']);

            if ($main_image) {
                foreach ($main_image as &$item) {
                    $filePath = $item['file'];
                    $parseFilePath = explode('/' , $filePath);
                    $end = end($parseFilePath);

                    if ($end_saved_image_selected == $end ) $item['selected'] = 'selected';
                }
            }


            return $main_image;
        } else {
            return [];
        }
    }

    public function getSelectedImage()
    {
        $template = $this->_coreRegistry->registry('giftcard_template');
        $model = $this->_templateFactory->create();
        $data = $model->load($template->getId())->getData();

        if (!empty($data)) {
            $end_saved_image_selected = '';
            $saved_image_selected = $data['saved_image_selected'];
            return $saved_image_selected;
        }
    }

    public function getTemplateId()
    {
        $template = $this->_coreRegistry->registry('giftcard_template');
        return $template->getId();
    }
}
