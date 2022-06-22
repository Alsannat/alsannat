<?php
namespace MGS\Popup\Block\Adminhtml\Popup\Edit\Tab;

class PopupInfomation extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

	/**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;
	
	/**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;
	
	/**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
	
	/**
     * @var \MGS\Popup\System\Config\Yesno
     */
    protected $_yesno;
	
	/**
     * @var \MGS\Popup\System\Config\Status
     */
    protected $_status;
	
	/**
     * @var \MGS\Popup\System\Config\Enableon
     */
	protected $_enableon;
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
		\Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
		\Magento\Framework\Convert\DataObject $objectConverter,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\MGS\Popup\Model\System\Config\Yesno $yesno,
		\MGS\Popup\Model\System\Config\Status $status,
		\MGS\Popup\Model\System\Config\Enableon $enableon,
        array $data = array()
    ) {
		$this->_wysiwygConfig = $wysiwygConfig;
        $this->_systemStore = $systemStore;
		$this->_objectConverter = $objectConverter;
		$this->_objectManager = $objectManager;
		$this->_yesno = $yesno;
		$this->_status = $status;
		$this->_enableon = $enableon;
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
        $model = $this->_coreRegistry->registry('mgs_popup');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Popup Content')));

        if ($model->getId()) {
            $fieldset->addField('popup_id', 'hidden', array('name' => 'popup_id'));
        }

		$fieldset->addField(
            'title',
            'text',
            array(
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
				'required' => true,
            )
        );
		
		$fieldset->addField(
            'css_html',
            'text',
            array(
                'name' => 'css_html',
                'label' => __('Custom Class'),
                'title' => __('Custom Class'),
				'required' => false,
            )
        );
		
		$fieldset->addField(
            'content_html',
            'editor',
            [
                'name' => 'content_html',
                'label' => __('Content Html'),
                'title' => __('Content Html'),
                'style' => 'height:25em',
                'required' => true,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
		
		$fieldset->addField(
            'enable_on',
            'select',
            [
				'name' => 'enable_on',
				'label' => __('Enable On'),
				'title' => __('Enable On'),
				'options' => $this->_enableon->toOptionArray()
			]
        );
		
		$groupOptions = $this->_objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection')->toOptionArray();
		$fieldset->addField(
			'customer_group',
			'multiselect',
			[
				'name' => 'customer_group[]',
				'label' => __('Enable For Customer Group'),
				'title' => __('Enable For Customer Group'),
				'values' => $groupOptions,
				'disabled' => $isElementDisabled,
				'note' => __('Blank to show for all customers and guest')
			]
		);
		
		$dateFormat = $this->_localeDate->getDateFormat(
            \IntlDateFormatter::SHORT
        );
		
		$fieldset->addField(
            'time_start',
            'date',
            [
                'name' => 'time_start',
                'label' => __('Start Date'),
                'title' => __('Start Date'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat
            ]
        );
		
		$fieldset->addField(
            'time_end',
            'date',
            [
                'name' => 'time_end',
                'label' => __('End Date'),
                'title' => __('End Date'),
                'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                'date_format' => $dateFormat,
				'time_format' => 'hh:mm:ss'
            ]
        );
		
		$fieldset->addField(
            'enb_cms',
            'select',
            [
				'name' => 'enb_cms',
				'label' => __('Show countdown time'),
				'title' => __('Show countdown time'),
				'options' => $this->_yesno->toOptionArray()
			]
        );
		
		$fieldset->addField(
            'check_closed',
            'select',
            [
				'name' => 'check_closed',
				'label' => __('Show "Don\'t show pupup again"'),
				'title' => __('Show "Don\'t show pupup again"'),
				'options' => $this->_yesno->toOptionArray()
			]
        );
		
		if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->_systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }
		
		$fieldset->addField(
            'status',
            'select',
            [
				'name' => 'status',
				'label' => __('Status'),
				'title' => __('Status'),
				'options' => $this->_status->toOptionArray()
			]
        );
		

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
        return __('Popup Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Popup Content');
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
