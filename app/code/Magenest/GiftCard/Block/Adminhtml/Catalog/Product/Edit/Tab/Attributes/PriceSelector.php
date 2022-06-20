<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/03/2016
 * Time: 16:07
 */

namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

class PriceSelector extends \Magento\Catalog\Block\Adminhtml\Helper\Form\Wysiwyg
{
    public function getElementHtml()
    {

        $html = $this->_layout->createBlock(
            'Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes\PriceSelectorRender',
            'giftcard.price.selector',
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
