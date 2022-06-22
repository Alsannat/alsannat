<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 24/09/2018
 * Time: 10:54
 */

namespace Wyomind\ElasticsearchCore\Block\Adminhtml\System\Config\Form\Field;


class LayerUpdateMethod implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Disabled')], ['value' => 0, 'label' => __('Hidden')]];
    }

    public function toArray()
    {
        return [0 => __('Disabled'), 1 => __('Hidden')];
    }
}