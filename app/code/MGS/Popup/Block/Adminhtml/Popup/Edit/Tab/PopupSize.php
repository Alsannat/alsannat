<?php
namespace MGS\Popup\Block\Adminhtml\Popup\Edit\Tab;

class PopupSize extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Popup Size')));

		$fieldset->addField(
            'popup_width',
            'text',
            array(
                'name' => 'popup_width',
                'label' => __('Popup Width (px)'),
                'title' => __('Popup Width (px)'),
				'required' => false,
				'note' => __('Default 800px'),
            )
        );
		
		$fieldset->addField(
            'popup_height',
            'text',
            array(
                'name' => 'popup_height',
                'label' => __('Popup Height (px)'),
                'title' => __('Popup Height (px)'),
				'required' => false,
				'note' => __('Default 450px'),
            )
        );

		$fieldset->addField(
            'padding_top',
            'text',
            array(
                'name' => 'padding_top',
                'label' => __('Padding Top (px)'),
                'title' => __('Padding Top (px)'),
				'required' => false
            )
        );
		
		$fieldset->addField(
            'padding_bottom',
            'text',
            array(
                'name' => 'padding_bottom',
                'label' => __('Padding Bottom (px)'),
                'title' => __('Padding Bottom (px)'),
				'required' => false
            )
        );
		
		$fieldset->addField(
            'popup_scroll',
            'select',
            [
				'name' => 'popup_scroll',
				'label' => __('Enable Scroll Bar'),
				'title' => __('Enable Scroll Bar'),
				'options' => $this->_yesno->toOptionArray()
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
        return __('Popup Size');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Popup Size');
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
