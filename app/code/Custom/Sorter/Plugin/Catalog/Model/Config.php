<?php

namespace Custom\Sorter\Plugin\Catalog\Model;

class Config
{
    public function afterGetAttributeUsedForSortByArray(\Magento\Catalog\Model\Config $catalogConfig, $options)
    {
        unset($options['name']);
       unset($options['price']);
        $options['position'] = __('Suggested');
        $options['new'] = __('New');
        $options['discount'] = __('Discount');
        $options['high_to_low'] = __('High To Low');
         $options['low_to_high'] = __('Low To High');
      
       



        return $options;
    }
}