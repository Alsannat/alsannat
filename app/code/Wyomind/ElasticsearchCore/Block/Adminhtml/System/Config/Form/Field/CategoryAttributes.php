<?php


namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;


class CategoryAttributes extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory
     */
    protected $_attributeCollectionFactory = null;

    protected $_data = [];

    protected $_requiredAttributes = ["name", "url_key", "meta_title", "description"];
    protected $_ignoredAttributes = ["custom_layout_update"];

    /**
     * CategoryAttributes constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory $attributeCollectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $this->_data);
        $this->_eavConfig = $eavConfig;
        $this->_attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
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

        $element->setData('value',json_encode($this->_data));

        $html = $element->getElementHtml();
        $eltId = $element->getId();


        $entityType = $this->_eavConfig->getEntityType('catalog_category');

        $collection = $this->_attributeCollectionFactory->create();
        $collection->setEntityTypeFilter($entityType->getEntityTypeId())
            ->addFieldToFilter('source_model', [
                ['neq' => 'eav/entity_attribute_source_boolean'],
                ['null' => true]
            ])
            ->addFieldToFilter(
                ['frontend_input', 'is_searchable'],
                [['in' => ['text', 'textarea']], '1']
            )
            ->addFieldToFilter('backend_type', ['nin' => ['static', 'decimal']]);


        foreach ($collection as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $tmp[] = [
                    //"attribute_id" => $attribute->getAttributeId(),
                    "code" => $attribute->getAttributeCode(),
                    "label" => $attribute->getDefaultFrontendLabel(),
                    "backend_type" => $attribute->getBackendType()
                ];
            }

        }

        usort($tmp, [$this, 'cmpAttributes']);

        $html .= "<div style='height:300px;overflow:auto'>";
        $html .= "<table id='" . $eltId . "_table'><tbody>";
        foreach ($tmp as $attribute) {
            if ($attribute['label'] != "") {
                $html .= "<tr attribute-code='" . $attribute['code'] . "' backend-type='" . $attribute['backend_type'] . "'>
                        <td><input class='category-attributes' type='checkbox'"
                    . (array_key_exists($attribute['code'], $this->_data) && $this->_data[$attribute['code']]['c'] == 1 ? " checked='checked'" : "")
                    . (in_array($attribute['code'], $this->_requiredAttributes) ? " style='display:none'" : "")
                    . "/></td>
                        <td> " . $attribute['label'] . " <i>[" . $attribute['code'] . "]</i></td>
                        <td style = 'width:100px; text-align:center' > "
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
                         var backend_type = tr.attr('backend-type'); 
                         json[tr.attr("attribute-code")] = {"c":selected?"1":"0", "b":backend_type};
                    });
                    $('#${eltId}').val(JSON.stringify(json));
                }
                $(document).on('change','.category-attributes', updateAttributesJson);
            });
            </script>
SCRIPT;

        return $html . $script;
    }

    public
    function cmpAttributes($a, $b)
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