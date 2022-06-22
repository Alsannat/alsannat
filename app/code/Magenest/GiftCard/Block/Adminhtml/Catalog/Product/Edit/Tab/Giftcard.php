<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 28/11/2015
 * Time: 15:41
 */
namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab;

class GiftCard extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'catalog/product/edit/tab/giftcard.phtml';


    /**
     * Return Tab label
     *
     * @return string
     * @api
     */
    public function getTabLabel()
    {
        return  __("Gift Card");
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return  __("Gift Card");
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
