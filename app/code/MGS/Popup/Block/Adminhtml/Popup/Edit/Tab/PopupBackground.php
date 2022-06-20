<?php
namespace MGS\Popup\Block\Adminhtml\Popup\Edit\Tab;

class PopupBackground extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
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
     * @var \MGS\Popup\System\Config\PositionX
     */
    protected $_positionx;
	
	/**
     * @var \MGS\Popup\System\Config\PositionY
     */
    protected $_positiony;
	
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
		\MGS\Popup\Model\System\Config\PositionX $positionx,
		\MGS\Popup\Model\System\Config\PositionY $positiony,
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
		$this->_positionx = $positionx;
		$this->_positiony = $positiony;
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

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Popup Background')));

		
		$fieldset->addField(
            'background_color',
            'text',
            array(
                'name' => 'background_color',
                'label' => __('Background Color'),
                'title' => __('Background Color'),
				'required' => false,
				'note' => __('Ex: #FFFFFF')
            )
        );
		
		$fieldset->addField(
            'background_image',
            'image',
            [
                'label' => __('Background Image'),
                'name' => 'background_image',
                'required' => false,
                'value' => $model->getBackgroundImage()
            ]
        );
		
		$fieldset->addField(
            'background_repeat',
            'select',
            [
				'name' => 'background_repeat',
				'label' => __('Background Repeat'),
				'title' => __('Background Repeat'),
				'options' => $this->_yesno->toOptionArray()
			]
        );
		
		$fieldset->addField(
            'background_cover',
            'select',
            [
				'name' => 'background_cover',
				'label' => __('Background Cover'),
				'title' => __('Background Cover'),
				'options' => $this->_yesno->toOptionArray()
			]
        );
		
		$fieldset->addField(
            'background_position_x',
            'select',
            [
				'name' => 'background_position_x',
				'label' => __('Background Position (x)'),
				'title' => __('Background Position (x)'),
				'options' => $this->_positionx->toOptionArray()
			]
        );
		
		$fieldset->addField(
            'background_position_y',
            'select',
            [
				'name' => 'background_position_y',
				'label' => __('Background Position (y)'),
				'title' => __('Background Position (y)'),
				'options' => $this->_positiony->toOptionArray()
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
        return __('Popup Background');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Popup Background');
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
