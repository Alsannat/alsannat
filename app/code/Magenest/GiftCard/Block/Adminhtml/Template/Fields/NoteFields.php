<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 13/02/2016
 * Time: 14:49
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class NoteFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_custom_note">';
        $html = $html.'X ';
        $html = $html.'<input id="template_custom_note_0" name="template[custom_note_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y ';
        $html = $html.'<input id="template_custom_note_1" name="template[custom_note_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Font Size ';
        $html = $html.'<input id="template_custom_note_2" name="template[custom_note_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';

        $html = $html.'Align ';
        $html = $html.'<select id="template_custom_note_align" name="template[custom_note_align]">';
        $html = $html.'<option value="0">Left</option>';
        $html = $html.'<option value="1">Right</option>';
        $html = $html.'</select>';
        $html = $html.'</div>';

        return $html;
    }
}
