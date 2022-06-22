<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 23:24
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container as FormContainer;
use Magento\Framework\Registry;

class Edit extends FormContainer
{
    protected $_coreRegistry;

    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'template_id';
        $this->_blockGroup = 'Magenest_GiftCard';
        $this->_controller = 'adminhtml_template';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Template'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );

        $this->buttonList->add(
            'preview',
            [
                'label' => __('Preview'),
            ]
        );

        $this->buttonList->update('delete', 'label', __('Delete Template'));
    }

    public function getHeaderText()
    {
        $templates = $this->_coreRegistry->registry('giftcard_template');
        if ($templates->getId()) {
            return __("Edit template '%1'", $this->escapeHtml($templates->getTitle()));
        }
        return __('New Templates');
    }

    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('giftcard/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
}
