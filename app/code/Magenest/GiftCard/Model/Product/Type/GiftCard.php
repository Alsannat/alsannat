<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 03/02/2016
 * Time: 10:49
 */
namespace Magenest\GiftCard\Model\Product\Type;

use Magento\Framework\App\ObjectManager;

class GiftCard extends \Magento\Catalog\Model\Product\Type\Virtual
{

    const TYPE_GIFTCARD = 'giftcard';

    public function hasOptions($product)
    {
        return true;
    }

}
