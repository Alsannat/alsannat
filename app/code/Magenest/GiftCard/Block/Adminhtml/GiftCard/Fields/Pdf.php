<?php
namespace Magenest\GiftCard\Block\Adminhtml\GiftCard\Fields;

use Magento\Framework\UrlInterface;

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 14/05/2017
 * Time: 21:50
 */
class Pdf extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('file');
    }
    public function getElementHtml()
    {
        $pdfUrl = $this->_getUrl();
        if ($pdfUrl) {
            $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $pdfUrl;

            $html= "<embed src='{$url}' height='100%'; width='100%' />";
        } else {
            $html ="";
        }
        return $html;
    }

    protected function _getUrl()
    {
        return $this->getValue();
    }
}