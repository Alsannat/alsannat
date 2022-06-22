<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class CurrencyId implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of Currency as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            '' => 'Select Currency',
            'SAR' => 'Saudi Riyal',
            'AED' => 'UAE Dirham',
            'USD' => 'American Dollar',
            'GBP' => 'British Pound Sterling',
            'OMR' => 'Omani Riyal',
            'JOD' => 'Jordanian dinar',
            'LBP' => 'Lebanese pound',
            'BHD' => 'Bahraini Dinar',
            'EGP' => 'Egyptian Pound',
            'KWD' => 'Kuwaiti Dinar',
            'CNY' => 'Yuan (Ren Min Bi)'

        ];
    }
}