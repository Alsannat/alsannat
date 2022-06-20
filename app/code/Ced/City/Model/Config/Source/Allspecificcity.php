<?php
namespace Ced\City\Model\Config\Source;

class Allspecificcity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('All Allowed City')],
            ['value' => 1, 'label' => __('Specific City')]
        ];
    }
}
