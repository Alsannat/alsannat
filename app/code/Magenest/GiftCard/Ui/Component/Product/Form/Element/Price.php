<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 08/07/2016
 * Time: 11:18
 */
namespace Magenest\GiftCard\Ui\Component\Product\Form\Element;

class Price extends \Magento\Ui\Component\Form\Element\AbstractElement
{
    const NAME = 'checkbox';

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
