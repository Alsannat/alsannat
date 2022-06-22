<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 16/02/2016
 * Time: 01:05
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class ToFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_custom_to">';
        $html = $html.'X ';
        $html = $html.'<input id="template_custom_to_0" name="template[custom_to_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y ';
        $html = $html.'<input id="template_custom_to_1" name="template[custom_to_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Font Size ';
        $html = $html.'<input id="template_custom_to_2" name="template[custom_to_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'</div>';

        return $html;
    }
}
