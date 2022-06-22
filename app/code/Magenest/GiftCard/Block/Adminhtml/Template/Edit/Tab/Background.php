<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 16/02/2016
 * Time: 22:12
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;
use Psr\Log\LoggerInterface as Logger;

class Background extends Generic implements TabInterface
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

    protected $_template = 'template/background.phtml';

    public function getTabLabel()
    {
        return __('Background Image Uploads');
    }

    public function getTabTitle()
    {
        return __('Background Image Uploads');
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

    public function getSavedBackgroundImage()
    {
        $template = $this->_coreRegistry->registry('giftcard_template');
        $model = $this->_templateFactory->create();
        $data = $model->load($template->getId())->getData();

        if (!empty($data)) {
            $endBGUrl= '';
            $saveBackground = unserialize($data['background_image']);
            $saveBackGroundUrl = $data['saved_background_selected'];
            if ($saveBackGroundUrl)  {
                $pareseUrlBG = explode('/' , $saveBackGroundUrl);
                $endBGUrl = end($pareseUrlBG);
            }

            if ($saveBackground) {
                foreach ($saveBackground as &$item) {
                    $filePath = $item['file'];
                    $parseFilePath = explode('/' , $filePath);
                    $end = end($parseFilePath);

                    if ($endBGUrl == $end ) $item['selected'] = 'selected';
                }
            }

            return $saveBackground;
        } else {
            return [];
        }
    }

    /**
     * @return mixed
     */
    public function getSelectedBackground() {
        $template = $this->_coreRegistry->registry('giftcard_template');
        $model = $this->_templateFactory->create();
        $data = $model->load($template->getId())->getData();

        if (!empty($data)) {
            $saveBackGroundUrl = $data['saved_background_selected'];
            return $saveBackGroundUrl;
        }
    }

    public function getTemplateId()
    {
        $template = $this->_coreRegistry->registry('giftcard_template');
        return $template->getId();
    }

}
