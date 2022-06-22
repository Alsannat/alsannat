<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 27/01/2016
 * Time: 13:42
 */
namespace Magenest\GiftCard\Model;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class Status extends AbstractSource
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;

    const DESIGN_LEFT = 1;
    const DESIGN_RIGHT = 2;
    const DESIGN_CENTER = 3;
    const DESIGN_CUSTOM = 4;

    public static function getOptionArray()
    {
        return [self::STATUS_ENABLED => __('Enable'), self::STATUS_DISABLED => __('Disable')];
    }

    public static function getDesignTypeArray()
    {
        return [
            self::DESIGN_LEFT => __('Left'),
            self::DESIGN_RIGHT => __('Right'),
            self::DESIGN_CENTER => __('Center'),
           // self::DESIGN_CUSTOM => __('Custom')
        ];
    }

    public function getAllOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }
        return $result;
    }

    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    public function getOptionGrid($optionId)
    {
        $options = self::getOptionArray();
        if ($optionId == self::STATUS_ENABLED) {
            $html = '<span class="grid-severity-notice"><span>' . $options[$optionId] . '</span>'.'</span>';
        } else {
            $html = '<span class="grid-severity-critical"><span>' . $options[$optionId] . '</span></span>';
        }

        return $html;
    }
}
