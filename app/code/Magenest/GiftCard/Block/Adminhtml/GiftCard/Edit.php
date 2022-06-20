<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/03/2016
 * Time: 08:58
 */
namespace Magenest\GiftCard\Block\Adminhtml\GiftCard;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * set the mode edit
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magenest_GiftCard';
        $this->_controller = 'adminhtml_giftCard';
        $this->_mode = 'edit';

        parent::_construct();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Edit Gift Card');
    }
}
