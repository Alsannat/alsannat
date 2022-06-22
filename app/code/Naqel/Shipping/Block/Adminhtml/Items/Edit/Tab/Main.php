<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Block\Adminhtml\Items\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
 
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\Registry $registry, 
        \Magento\Framework\Data\FormFactory $formFactory,  
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig, 
        array $data = []
    ) 
    {
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('City Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('City Information');
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

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_naqel_shipping_items');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('item_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('City Information')]);
        if ($model->getId()) {
            $fieldset->addField('naqel_city_id', 'hidden', ['name' => 'naqel_city_id']);
        }
        $fieldset->addField(
            'code',
            'text',
            ['name' => 'code', 'label' => __('Code'), 'title' => __('Code'), 'required' => true]
        );
        
        $fieldset->addField(
            'client_city_name',
            'text',
            ['name' => 'client_city_name', 'label' => __('Client city name'), 'title' => __('Client city name'), 'required' => false]
        );
        
        $fieldset->addField(
            'client_city_name_ar',
            'text',
            ['name' => 'client_city_name_ar', 'label' => __('Client city name in Arabic'), 'title' => __('Client city name in Arabic'), 'required' => false]
        );
        
        $fieldset->addField(
            'country_code',
            'text',
            ['name' => 'country_code', 'label' => __('Country Code'), 'title' => __('Country Code'), 'required' => true]
        );
        
        $fieldset->addField(
            'client_country_name',
            'text',
            ['name' => 'client_country_name', 'label' => __('Client country name'), 'title' => __('Client country name'), 'required' => false]
        );
        /*$fieldset->addField(
            'city_name',
            'text',
            ['name' => 'city_name', 'label' => __('City Name'), 'title' => __('City Name'), 'required' => true]
        );*/
        

        /*$fieldset->addField(
            'oda',
            'text',
            ['name' => 'oda', 'label' => __('Oda'), 'title' => __('Oda'), 'required' => true]
        );
        $fieldset->addField(
            'city_longitude',
            'text',
            ['name' => 'city_longitude', 'label' => __('Longitude'), 'title' => __('Longitude'), 'required' => false]
        );
        $fieldset->addField(
            'city_lattitude',
            'text',
            ['name' => 'city_lattitude', 'label' => __('Lattitude'), 'title' => __('Lattitude'), 'required' => false]
        );*/

        
        $fieldset->addField(
            'status',
            'select',
            ['name' => 'status', 'label' => __('Status'), 'title' => __('Status'),  'options'   => [0 => 'Disable', 1 => 'Enable'], 'required' => true]
        );
        
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
