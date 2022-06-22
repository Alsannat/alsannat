<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 13/02/2016
 * Time: 14:23
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class GiftkeyFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_custom_giftkey">';
        $html = $html.'X ';
        $html = $html.'<input id="template_custom_giftkey_0" name="template[custom_giftkey_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y ';
        $html = $html.'<input id="template_custom_giftkey_1" name="template[custom_giftkey_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Font Size ';
        $html = $html.'<input id="template_custom_giftkey_2" name="template[custom_giftkey_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'</div>';

        return $html;
    }
}
