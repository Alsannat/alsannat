<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 02/03/2016
 * Time: 16:01
 */
namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

use Magento\Backend\Block\Widget;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Template extends \Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg
{
    protected $_template = 'catalog/product/edit/tab/attribute/template.phtml';
    protected $templateFactory;
    protected $productRepo;
    protected $productFactory;
    protected $_coreRegistry;
    /**
     * @var \Magenest\GiftCard\Model\TemplateFactory
     */
    protected $_templateFactory;




    public function getElementHtml()
    {

        $html = $this->_layout->createBlock(
            'Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\Render',
            '',
            [
                'data' => [
                    'label' => __('WYSIWYG Editor'),
                    'type' => 'button',

                    'class' => 'action-wysiwyg'

                ]
            ]
        )->toHtml();
        return $html;
    }
}
