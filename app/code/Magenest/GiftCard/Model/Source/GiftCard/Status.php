<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 21/03/2016
 * Time: 14:17
 */
namespace Magenest\GiftCard\Model\Source\GiftCard;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /**#@+
     * Product Status values
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;
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
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
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
