<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 13/02/2016
 * Time: 00:23
 */
namespace Magenest\GiftCard\Block\Adminhtml\Template\Fields;

class BarcodeImageFields extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $html = '<div id="template_barcode_image">';
        $html = $html.'X1 ';
        $html = $html.'<input id="template_barcode_image_0" name="template[barcode_image_0]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y1 ';
        $html = $html.'<input id="template_barcode_image_1" name="template[barcode_image_1]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'X2 ';
        $html = $html.'<input id="template_barcode_image_2" name="template[barcode_image_2]" class="validate-number input-text" type="text" style="width: 15%; margin-right: 10px"/>';
        $html = $html.'Y2 ';
        $html = $html.'<input id="template_barcode_image_3" name="template[barcode_image_3]" class="validate-number input-text" type="text" style="width: 15%"/>';
        $html = $html.'</div>';

        return $html;
    }
}
