<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 29/01/2016
 * Time: 00:34
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template;

class Img extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '';
        $html = $html.'<img id="template_design_view" src="" style="width:100%"/>';

        return $html;
    }
}
