<?php
namespace Magenest\GiftCard\Model\Source\Design;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 13/05/2017
 * Time: 13:59
 */
class Mode extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#
     * Product Status values
     */
    const CANVAS_MODE = 1;

    const PDF_MODE = 0;

    const TURN_OFF_DESIGN = 3;
    /**
     * Retrieve All options
     *
     * @return array
     */
    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [self::CANVAS_MODE => __('Canvas Mode'),
            self::PDF_MODE => __('Pdf Mode'),
            self::TURN_OFF_DESIGN => __('Turn off design mode')
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