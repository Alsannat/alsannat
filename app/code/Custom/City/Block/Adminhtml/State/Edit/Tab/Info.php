<?php

namespace Custom\City\Block\Adminhtml\State\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
class Info extends Generic implements TabInterface
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var $model Custom\City\Model\City */
        $model = $this->_coreRegistry->registry('city_state');
        $data = $model->getData();

        if(count($data)==0){
            $data = $this->_session->getFormData();
        }
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('state');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General')]
        );

        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'id']
            );
        }
        $fieldset->addField(
            'default_name',
            'text',
            [
                'name'        => 'default_name',
                'label'    => __('State Name'),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'arabic_name',
            'text',
            [
                'name'        => 'arabic_name',
                'label'    => __('State AR Name'),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'code',
            'text',
            [
                'name'        => 'code',
                'label'    => __('State Code'),
                'required'     => true
            ]
        );
        $country = $fieldset->addField(
            'country_id',
            'select',
            [
                'name'      => 'country_id',
                'label'     => __('Country'),
                'title' => __('Country'),
                'values'   => $this->_countryFactory->toOptionArray(),
                'required'     => true
            ]
        );
        // if(count($data) > 0 && isset($data['region_id'])){
            $data['id'] = $data['region_id'];
        // }
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('State Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('State Info');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
 