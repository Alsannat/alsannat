<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class Stickersize implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of Sticker size as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => 'Select Sticker size',
            'FourMEightInches' => '4 x 8 Inches',
            'FourMSixthInches' => '4 x 6 Inches',
            'A4' => 'A4'
            
        ];
    }
}