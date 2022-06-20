<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 16/03/2016
 * Time: 22:23
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class ExpiryFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_custom_expiry">';
        $html = $html.'X ';
        $html = $html.'<input id="template_custom_expiry_0" name="template[custom_expiry_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y ';
        $html = $html.'<input id="template_custom_expiry_1" name="template[custom_expiry_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Font Size ';
        $html = $html.'<input id="template_custom_expiry_2" name="template[custom_expiry_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';

        $html = $html.'</div>';

        return $html;
    }
}
