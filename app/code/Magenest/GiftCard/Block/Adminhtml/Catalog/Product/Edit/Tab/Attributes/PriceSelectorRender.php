<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/03/2016
 * Time: 16:08
 */

namespace Magenest\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

class PriceSelectorRender extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Price\Group\AbstractGroup
{
    protected $_template = 'catalog/product/edit/price/select.phtml';

    public function getPrices()
    {
        return  $this->getProduct()->getData('giftcard_price_selector');
    }
}
