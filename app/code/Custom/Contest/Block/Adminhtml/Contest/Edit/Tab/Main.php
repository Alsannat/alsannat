<?php
namespace Custom\Contest\Block\Adminhtml\Contest\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('contest');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Custom_Contest::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('contest_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Contest Information')]);

        if ($model->getId()) {
            $fieldset->addField('contest_id', 'hidden', ['name' => 'contest_id']);
        }

        $fieldset->addField(
            'first_name',
            'text',
            [
                'name' => 'first_name',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'father_name',
            'text',
            [
                'name' => 'father_name',
                'label' => __('Father Name'),
                'title' => __('Father Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'last_name',
            'text',
            [
                'name' => 'last_name',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'phone_no',
            'text',
            [
                'name' => 'phone_no',
                'label' => __('Phone Number'),
                'title' => __('Phone Number'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'gender',
            'text',
            [
                'name' => 'gender',
                'label' => __('Gender'),
                'title' => __('Gender'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'nationality',
            'text',
            [
                'name' => 'nationality',
                'label' => __('Nationality'),
                'title' => __('Nationality'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'age_group',
            'text',
            [
                'name' => 'age_group',
                'label' => __('Age Group'),
                'title' => __('Age Group'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'about_us',
            'text',
            [
                'name' => 'about_us',
                'label' => __('About Us'),
                'title' => __('About Us'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

       
        
        /*$dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $fieldset->addField('published_at', 'date', [
            'name'     => 'published_at',
            'date_format' => $dateFormat,
            'image'    => $this->getViewFileUrl('images/grid-cal.gif'),
            'value' => $model->getPublishedAt(),
            'label'    => __('Publishing Date'),
            'title'    => __('Publishing Date'),
            'required' => true
        ]);*/
        
        $this->_eventManager->dispatch('adminhtml_contest_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
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
        return __('Contest Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Contest Information');
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
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
