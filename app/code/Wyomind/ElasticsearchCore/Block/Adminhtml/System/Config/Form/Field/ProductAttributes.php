<?php


namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;


class ProductAttributes extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface|null
     */
    protected $_attributeRepository = null;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|null
     */
    protected $_searchCriteriaBuilder = null;

    protected $_data = [];

    protected $_requiredAttributes = ["sku", "name"];
    protected $_ignoredAttributes = ["product_weight", "tax_class_id", "visibility", "price", "status", "category_ids", "image"];

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    )
    {
        parent::__construct($context, $this->_data);
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_attributeRepository = $attributeRepository;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $value = $element->getData('value');


        try {
            $this->_data = json_decode($value, true);
            if ($this->_data === null) {
                $this->_data = [];
            }
        } catch (\Exception $e) {
            $this->_data = [];
        }

        $element->setData('value', json_encode($this->_data));

        $html = $element->getElementHtml();
        $eltId = $element->getId();

        $typeCode = \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE;
        $attributeList = $this->_attributeRepository->getList($typeCode, $this->_searchCriteriaBuilder->create())->getItems();

        $tmp = [];
        foreach ($attributeList as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $this->_ignoredAttributes)) {
                $tmp[] = [
                    //"attribute_id" => $attribute->getAttributeId(),
                    "code" => $attribute->getAttributeCode(),
                    "label" => $attribute->getDefaultFrontendLabel(),
                    "backend_type" => $attribute->getBackendType()
                ];
            }
        }
        usort($tmp, [$this, 'cmpAttributes']);

        $html .= "<table style='font-weight: bold'><thead><tr><td></td><td>" . __('Attribute') . "</td><td style='width:100px; text-align:center'>" . __("Weight") . "</td></tr></thead></table>";
        $html .= "<br/><div style='height:300px;overflow:auto'>";
        $html .= "<table id='" . $eltId . "_table'><tbody>";
        foreach ($tmp as $attribute) {
            if ($attribute['label'] != "") {
                $html .= "<tr attribute-code='" . $attribute['code'] . "' backend-type='" . $attribute['backend_type'] . "'>"
                    . "<td><input class='product-attributes' type='checkbox'"
                    . (array_key_exists($attribute['code'], $this->_data) && $this->_data[$attribute['code']]['c'] == 1 ? " checked='checked'" : "")
                    . (in_array($attribute['code'], $this->_requiredAttributes) ? "style='display:none'" : "")
                    . " /></td>"
                    . "<td> " . $attribute['label'] . " <i>[" . $attribute['code'] . "]</i></td>
                        <td style = 'width:100px; text-align:center' > "
                    . $this->getSelect(array_key_exists($attribute['code'], $this->_data) ? $this->_data[$attribute['code']]['w'] : 1)
                    . "</td>
                        </tr> ";
            }
        }
        $html .= "</tbody > ";
        $html .= "</table ></div > ";


        $script = <<<SCRIPT
            <script>
            require(["jquery","underscore"],function($,_) {
                function updateAttributesJson() {
                    var table = $('#${eltId}_table');
                    var json = {};
                    _.each(table.find('tr'), function(tr) {
                        tr = $(tr);
                         var selected = $(tr.find("input[type=checkbox]")[0]).prop("checked");
                         var weight = $(tr.find("select")[0]).val();
                         var backend_type = tr.attr('backend-type'); 
                         json[tr.attr("attribute-code")] = {"c":selected?"1":0, "w":weight, "b":backend_type};
                    });
                    $('#${eltId}').val(JSON.stringify(json));
                }
                $(document).on('change','.product-attributes', updateAttributesJson);
            });
            </script>
SCRIPT;

        return $html . $script;
    }

    public function getSelect($selected)
    {
        return "<select class='product-attributes' style='width:50px'>
                <option value = '10' " . ($selected == 10 ? "selected='selected'" : "") . ">10</option >
                <option value = '9' " . ($selected == 9 ? "selected='selected'" : "") . ">9</option >
                <option value = '8' " . ($selected == 8 ? "selected='selected'" : "") . ">8</option >
                <option value = '7' " . ($selected == 7 ? "selected='selected'" : "") . ">7</option >
                <option value = '6' " . ($selected == 6 ? "selected='selected'" : "") . ">6</option >
                <option value = '5' " . ($selected == 5 ? "selected='selected'" : "") . ">5</option >
                <option value = '4' " . ($selected == 4 ? "selected='selected'" : "") . ">4</option >
                <option value = '3' " . ($selected == 3 ? "selected='selected'" : "") . ">3</option >
                <option value = '2' " . ($selected == 2 ? "selected='selected'" : "") . ">2</option >
                <option value = '1' " . ($selected == 1 ? "selected='selected'" : "") . ">1</option >
                </select > ";
    }

    public function cmpAttributes($a, $b)
    {
        if (in_array($a['code'], $this->_requiredAttributes) && in_array($b['code'], $this->_requiredAttributes)) {
            return ($a['label'] < $b['label']) ? -1 : 1;
        } elseif (in_array($a['code'], $this->_requiredAttributes)) {
            return -1;
        } elseif (in_array($b['code'], $this->_requiredAttributes)) {
            return 1;
        }

        if (isset($this->_data[$a['code']]) && !isset($this->_data[$b['code']])) {
            return -1;
        } elseif (!isset($this->_data[$a['code']]) && isset($this->_data[$b['code']])) {
            return 1;
        } elseif (isset($this->_data[$a['code']]) && isset($this->_data[$b['code']])) {
            if ($this->_data[$a['code']]['c'] == 1 && $this->_data[$b['code']]['c'] == 1) {
                return ($a['label'] < $b['label']) ? -1 : 1;
            } elseif ($this->_data[$a['code']]['c'] == 1) {
                return -1;
            } elseif ($this->_data[$b['code']]['c'] == 1) {
                return 1;
            } else {
            return ($a['label'] < $b['label']) ? -1 : 1;
            }
        } else {
            return ($a['label'] < $b['label']) ? -1 : 1;
        }
    }

}