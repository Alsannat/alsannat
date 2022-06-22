<?php

namespace Custom\City\Block\Adminhtml\Zip\Import\Edit\Tab;

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
     * @param \Magento\Directory\Model\Config\Source\Country $countryFactory
     * @param \Custom\City\Model\StateFactory $stateFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $wysiwygConfig,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        \Custom\City\Model\StateFactory $stateFactory,
        array $data = []
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_countryFactory = $countryFactory;
        $this->_stateFactory = $stateFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $data = array();
        $data = $this->_session->getFormData();
        $this->_session->setFormData(null);
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('');
        $form->setFieldNameSuffix('import_zip');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Upload CSV File For').__('Neighborhoods')]
        );
        $country = $fieldset->addField(
            'country_id',
            'select',
            [
                'name'      => 'country_id',
                'label'     => __('Country'),
                'title' => __('Country'),
                'values'   => $this->_countryFactory->toOptionArray(),
                'required'     => true,
                'class'     => 'validate-select'
            ]
        );
        $_states_options = array();
        $_states_options[''] = 'Please Select';
        if($data['country_id']!=""){
            $_states = $this->_stateFactory->create()->getCollection();
            $_states = $_states->addFieldToFilter('country_id',$data['country_id']);
            if(count($_states->getData()) > 0){
                foreach ($_states as $_state){
                    $_states_options[$_state['region_id']] = $_state['default_name'];
                }
            }
        }
        $fieldset->addField(
            'state_id',
            'select',
            [
                'name'      => 'state_id',
                'label'     => __('State'),
                'values' =>  $_states_options,
                'required'     => false,
                'note' => 'State is optional now, if you have state then select state.'
            ]
        );
        $country->setAfterElementHtml("
            <script type=\"text/javascript\">
                    require([
                    'jquery',
                    'mage/template',
                    'jquery/ui',
                    'mage/translate'
                ],
                function($, mageTemplate) {
                   $('#edit_form').on('change', '#country_id', function(event){
                        $.ajax({
                               url : '". $this->getUrl('city/city/regionlist') . "country/' +  $('#country_id').val(),
                                type: 'get',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                    $('#state_id').empty();
                                    $('#state_id').append(data.htmlconent);
                               }
                            });
                   })
                }

            );
            </script>"
        );
        $additional = $fieldset->addField(
            'file',
            'file',
            [
                'name'      => 'csv',
                'label'     => __('File'),
                'title' => __('Upload CSV'),
                'required'     => true,
                'class'     => 'required-file validate-fileextensions',
            ]
        );
        $additional->setAfterElementHtml("<script type=\"text/javascript\">
			require([
				'jquery',
				'jquery/ui',
				'jquery/validate',
				'mage/translate'
			], function ($) {
				 $.validator.addMethod(
					'validate-fileextensions', function (v, elm) {

						var extensions = ['csv'];
						if (!v) {
							return true;
						}
						with (elm) {
							var ext = value.substring(value.lastIndexOf('.') + 1);
							for (i = 0; i < extensions.length; i++) {
								if (ext == extensions[i]) {
									return true;
								}
							}
						}
						return false;
					}, $.mage.__('Disallowed file type, only csv extension is allowed.'));
			});
			</script>
		");

        $fieldset->addField(
            'link',
            'link',
            [
                'name'      => 'sample_download_link',
                'label'     => __('Download Sample'),
                'link_label'=>__('Download Sample'),
                'title' => __('Download Sample CSV'),
                'value'   =>  __('Download Sample'),
                'href'     => $this->getBaseUrl(). 'var/import/zipsimport.csv',
                'note'     => '<br />'.__('Zip codes').__('import file will be in csv format and contains two columns like below:').'<br> <table border="1" width="100%"><tr><th>Zipcode</th><th>City</th></tr><tr><td align="center">12345</td><td align="center">Abcd</td></tr></table>
                <br /> Now you can add 3rd column state in zip codes csv so you can import multiple zipcodes in multiple states directly instead adding zipcodes in state one by one, add 3rd colum State like below:<br />'.
                    '<table border="1" width="100%"><tr><th>Zipcode</th><th>City</th><th>State</th></tr><tr><td align="center">12345</td><td align="center">Abcd</td><td align="center">xyz</td></tr></table>'
            ]
        );
        $data['link'] = __('Download Sample CSV');
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
        return __('Zip Codes Import Info');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Zip Codes Import Info');
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
 