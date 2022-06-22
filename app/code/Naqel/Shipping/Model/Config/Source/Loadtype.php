<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model\Config\Source;

class Loadtype implements \Magento\Framework\Option\ArrayInterface
{ 
    /**
     * Return array of load type as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ''=>'Select load type',
            1  => 'Express',
            2  => 'HW',
            3  => 'LTL',
            5  => 'Box',
            7  => 'Pallet',
            9  => 'Half Load',
            10 => 'Full Load',
            11 => 'C12D',
            12 => 'C12P',
            13 => 'C12PAID',
            14 => 'C9D',
            15 => 'C9P',
            16 => 'CSD',
            17 => 'CSP',
            18 => 'WED',
            19 => 'WEP',
            20 => 'WEE',
            26 => 'FF',
            27 => 'GCC',
            28 => 'Prepaid',
            29 => 'Drums',
            30 => "Express Int'l",
            31 => "FTL Int'l",
            32 => "Courier Int'l",
            33 => "Document Int'l",
            34 => "Non Document Int'l",
            35 => "Document",
            36 => "Non Document",
            37 => "FTL Reefer",
            38 => "FTL-Drop Side",
            39 => "Express Domestic",
            40 => "HW Domestic",
            41 => "NAQEL Express Box 10 Kg",

        ];
    }
}