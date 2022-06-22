<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 28/01/2016
 * Time: 13:27
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Gift Card Template Configuration'));
    }
}
