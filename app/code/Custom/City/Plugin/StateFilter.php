<?php
namespace Custom\City\Plugin;
class StateFilter
{
    public function afterToOptionArray(\Magento\Directory\Model\ResourceModel\Region\Collection $subject, $options){
        $allOptions = [];
        foreach ($options as $option) {
            if (isset($option['label'])){
                $option['label'] = __($option['label']);
            }
            $allOptions[] = $option;
        }
        return (count($allOptions) > 0) ? $allOptions : $options;
    }
}