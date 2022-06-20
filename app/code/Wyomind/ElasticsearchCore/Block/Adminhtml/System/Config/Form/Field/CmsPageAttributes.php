<?php


namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;


class CmsPageAttributes extends \Magento\Config\Block\System\Config\Form\Field
{


    /**
     * @var \Magento\Cms\Model\ResourceModel\Page
     */
    protected $_pageResource = null;

    protected $_data = [];

    protected $_requiredAttributes = ["title", "content", "identifier"];
    protected $_ignoredAttributes = ["layout_update_xml"];

    protected $allowedTypes = [
        'char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext'
    ];

    /**
     * CmsPageAttributes constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Cms\Model\ResourceModel\Page $pageResource
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Cms\Model\ResourceModel\Page $pageResource,
        array $data = []
    )
    {
        parent::__construct($context, $this->_data);
        $this->_pageResource = $pageResource;
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


        $options = [];
        $tableInfo = $this->_pageResource->getConnection()->describeTable($this->_pageResource->getMainTable());

        foreach ($tableInfo as $field => $info) {
            if (in_array($info['DATA_TYPE'], $this->allowedTypes) &&
                $field != 'layout_update_xml' &&
                substr($field, 0, 7) !== 'custom_') {
                $tmp[] = [
                    //"attribute_id" => $attribute->getAttributeId(),
                    "code" => $field,
                    "label" => ucwords(strtr($field, '_-', '  ')),
                    "backend_type" => "text"
                ];
            }
        }



        usort($tmp, [$this, 'cmpAttributes']);

        $html .= "<div style='height:300px;overflow:auto'>";
        $html .= "<table id='" . $eltId . "_table'><tbody>";
        foreach ($tmp as $attribute) {
            if ($attribute['label'] != "") {
                $html .= "<tr attribute-code='" . $attribute['code'] . "' backend-type='" . $attribute['backend_type'] . "'>
                        <td><input class='cms-attributes' type='checkbox'"
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
                $(document).on('change','.cms-attributes', updateAttributesJson);
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