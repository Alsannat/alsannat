<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class Billtype implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of Bill type as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => 'Select Bill type',
            1 => 'On Account',
            2 => 'Cash',
            5 => 'Cash On Delivery'
        ];
    }
}