<?php


namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;


class ColorPicker extends \Magento\Config\Block\System\Config\Form\Field
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context, array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                $(document).ready(function () {
                    var $elementId = $("#' . $element->getHtmlId() . '");
                    $elementId.css("backgroundColor", "' . $value . '");
                    $elementId.ColorPicker({
                        color: "' . $value . '",
                        onChange: function (hsb, hex, rgb) {
                            $elementId.css("backgroundColor", "#" + hex).val("#" + hex);
                        }
                    }).bind("keyup", function(){
                        $(this).ColorPickerSetColor(this.value);
                        if (this.value.startsWith("#")) {
                            $elementId.css("backgroundColor", this.value).val(this.value);
                        } else {
                            $elementId.css("backgroundColor", "#" + this.value).val("#" + this.value);
                        }
                    });
                });
            });
            </script>';
        return $html;
    }
}