<?php

namespace Evincemage\City\Block\Adminhtml\Grid\Edit;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Store\Model\System\Store;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_systemStore;
    protected $_availcourier;
    protected $_allowCountry;
    


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Evincemage\City\Model\Source\AvailShipMethods $availcourier,
        \Evincemage\City\Model\Source\AllowCountry $allowCountry,
        Store $systemStore,
        array $data = []
    ) {
        $this->_availcourier = $availcourier;
        $this->_allowCountry = $allowCountry;
        $this->_systemStore           = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form',
                            'enctype' => 'multipart/form-data',
                            'action' => $this->getData('action'),
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('evincegrid_');
        if ($model->getEntityId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit City'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add City'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'city_ar',
            'text',
            [
                'name' => 'city_ar',
                'label' => __('City Arabic'),
                'id' => 'city_ar',
                'title' => __('City Arabic'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'city_en',
            'text',
            [
                'name' => 'city_arabic',
                'label' => __('City English'),
                'id' => 'city_ar',
                'title' => __('City English'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        $fieldset->addField(
            'naquel_city',
            'text',
            [
                'name' => 'naquel_city',
                'label' => __('Naquel City'),
                'id' => 'naquel_city',
                'title' => __('Naquel City'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'country_code',
            'select',
            [
                'name' => 'country_code',
                'label' => __('Country'),
                'id' => 'country_code',
                'title' => __('Country'),
                'class' => 'required-entry',
                'required' => false,
                'values' => $this->_allowCountry->getAllowedCountries(),
            ]
        );
        //$model->setData('courier', $categories);
//        $fieldset->addField(
//            'courier',
//            'multiselect',
//            [
//                'label' => __('Courier'),
//                'title' => __('Courier'),
//                'name' => 'courier',
//                'required' => true,
//                'values' => $this->_availcourier->getActiveShippingMethod(),
//            ]
//        );
        /*$fieldset->addField(
            'courier',
            'select',
            [
                'label' => __('Courier'),
                'title' => __('Courier'),
                'name' => 'courier',
                'required' => true,
                'values' => $this->_availcourier->toOptionArray(),
            ]
        );*/
        if ($this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('store_ids', 'hidden', [
                'name'  => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        } else {
            /** @var RendererInterface $rendererBlock */
            //$rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'select', [
                'name'     => 'store_ids',
                'label'    => __('Store Views'),
                'title'    => __('Store Views'),
                'required' => true,
                'values'   => $this->_systemStore->getStoreValuesForForm(false, true)
            ]);
        }  
        
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
