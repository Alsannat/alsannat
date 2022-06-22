<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 28/01/2016
 * Time: 13:36
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Edit\Tab;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;
use Psr\Log\LoggerInterface as Logger;

class General extends Generic implements TabInterface
{
    protected $_status;

    protected $_logger;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magenest\GiftCard\Model\Status $status,
        array $data
    ) {
        $this->_logger = $context->getLogger();
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('giftcard_template');

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');

        $fieldset = $form->addFieldset(
            'general_fieldset',
            [
                'legend' => __('General Configuration'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addType('img', '\Magenest\GiftCard\Block\Adminhtml\Template\Img');
        $fieldset->addType('mainimage', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\MainImageFields');
        $fieldset->addType('boximage', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\BoxImageFields');
        $fieldset->addType('barcodeimage', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\BarcodeImageFields');
        $fieldset->addType('title', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\TitleFields');
        $fieldset->addType('from', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\FromFields');
        $fieldset->addType('to', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\ToFields');
        $fieldset->addType('giftkey', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\GiftkeyFields');
        $fieldset->addType('cardvalue', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\ValueFields');
        $fieldset->addType('displayvalue', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\DisplayValueFields');
        $fieldset->addType('customnote', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\NoteFields');
        $fieldset->addType('expiry', 'Magenest\GiftCard\Block\Adminhtml\Template\Fields\ExpiryFields');

        if ($model->getId()) {
            $fieldset->addField(
                'template_id',
                'hidden',
                ['name' => 'template[id]']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'template[name]',
                'label' => __('Template Name'),
                'title' => __('Template Name'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'template[status]',
                'required' => true,
                'options' => $this->_status->getOptionArray(),
            ]
        );

        $fieldset->addField(
            'design_type',
            'select',
            [
                'label' => __('Design Style'),
                'title' => __('Design Style'),
                'name' => 'template[design_type]',
                'required' => true,
                'options' => $this->_status->getDesignTypeArray(),
                'onchange' => 'changeDesignType(this);'
            ]
        );

        $fieldset->addField(
            'main_image',
            'mainimage',
            [
                'label' => __('Main Image Coordinates'),
                'title' => __('Main Image Coordinates')
            ]
        );

        $fieldset->addField(
            'box_image',
            'boximage',
            [
                'label' => __('Message Box Coordinates'),
                'title' => __('Message Box Coordinates')
            ]
        );

        $fieldset->addField(
            'barcode_image',
            'barcodeimage',
            [
                'label' => __('Barcode Coordinates'),
                'title' => __('Barcode Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_title',
            'title',
            [
                'label' => __('Title Coordinates'),
                'title' => __('Title Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_from',
            'from',
            [
                'label' => __('"From" Coordinates'),
                'title' => __('"From" Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_to',
            'to',
            [
                'label' => __('"To" Coordinates'),
                'title' => __('"To" Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_giftkey',
            'giftkey',
            [
                'label' => __('Gift Key Coordinates'),
                'title' => __('Gift Key Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_value',
            'cardvalue',
            [
                'label' => __('"Value" String Coordinates'),
                'title' => __('"Value" String Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_display_value',
            'displayvalue',
            [
                'label' => __('Value Number Coordinates'),
                'title' => __('Value Number Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_expiry',
            'expiry',
            [
                'label' => __('Expiry Date Coordinates'),
                'title' => __('Expiry Date Coordinates')
            ]
        );

        $fieldset->addField(
            'custom_note',
            'customnote',
            [
                'label' => __('Note Coordinates'),
                'title' => __('Note Coordinates')
            ]
        );

        $fieldset->addField(
            'design_view',
            'img',
            [
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'template[title]',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true
            ]
        );

        $fieldset->addField(
            'style_color',
            'text',
            [
                'label' => __("Style Color"),
                'title' => __("Style Color"),
                'required' => true,
                'class' => 'jscolor {hash:true, refine:false, adjust: false}',
                'name' => 'template[style_color]',
                'style' => 'width: 30%',
                'note' => 'This color will be applied for title and value field of the card'
            ]
        );

        $fieldset->addField(
            'text_color',
            'text',
            [
                'label' => __("Text Color"),
                'title' => __("Text Color"),
                'required' => true,
                'class' => 'jscolor {hash:true, refine:false, adjust: false}',
                'name' => 'template[text_color]',
                'style' => 'width: 30%',
                'note' => 'This color will be applied for other text fields of the card'
            ]
        );

        $fieldset->addField(
            'note',
            'textarea',
            [
                'name' => 'template[note]',
                'label' => __('Note'),
                'title' => __('Note'),
                'note' => 'Some reminder that will be printed on the card'
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTemplateData()
    {
        $model = $this->_coreRegistry->registry('giftcard_template');
        return $model->getData();
    }

    public function getTabLabel()
    {
        return __('General Configuration');
    }

    public function getTabTitle()
    {
        return __('General Configuration');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
