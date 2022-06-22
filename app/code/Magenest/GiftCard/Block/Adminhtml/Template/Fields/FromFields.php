<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 13/02/2016
 * Time: 13:53
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class FromFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_custom_from">';
        $html = $html.'X ';
        $html = $html.'<input id="template_custom_from_0" name="template[custom_from_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y ';
        $html = $html.'<input id="template_custom_from_1" name="template[custom_from_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Font Size ';
        $html = $html.'<input id="template_custom_from_2" name="template[custom_from_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'</div>';

        return $html;
    }
}
