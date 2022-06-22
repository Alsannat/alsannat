<?php
/**
 * Created by Magenest.
 * Author: Pham Quang Hau
 * Date: 30/03/2016
 * Time: 05:25
 */
namespace Magenest\GiftCard\Model\Source\Template;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class DesignType extends AbstractSource implements SourceInterface, OptionSourceInterface
{

    const DESIGN_LEFT = 1;
    const DESIGN_RIGHT = 2;
    const DESIGN_CENTER = 3;
    const DESIGN_CUSTOM = 4;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::DESIGN_LEFT => __('Left'),
            self::DESIGN_RIGHT => __('Right'),
            self::DESIGN_CENTER => __('Center'),
           // self::DESIGN_CUSTOM => __('Custom')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
