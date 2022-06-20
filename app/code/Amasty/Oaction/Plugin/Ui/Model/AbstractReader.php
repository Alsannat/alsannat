<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Oaction
 */


declare(strict_types=1);

namespace Amasty\Oaction\Plugin\Ui\Model;

use Amasty\Oaction\Helper\Data;
use Amasty\Oaction\Model\OrderAttributesChecker;
use Amasty\Oaction\Model\Source\State;
use Magento\Framework\Module\Manager;

class AbstractReader
{
    const UI_COMPONENT = 'Amasty_Oaction/js/grid/tree-massactions';

    const ACTION_SIGN = 'amasty_oaction';

    const ACTION_DELIMITER = 'amasty_oaction_delemiter';

    const ACTION_STATUS = 'amasty_oaction_status';

    const ACTION_ORDER_ATTRIBUTES = 'amasty_oaction_orderattr';

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var State
     */
    private $orderStatus;

    /**
     * @var array
     */
    private $availableActions = [];

    public function __construct(
        Data $helper,
        State $orderStatus,
        Manager $moduleManager
    ) {
        $this->helper = $helper;
        $this->orderStatus = $orderStatus;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function updateMassactions(array $result): array
    {
        if (isset($result['children']['listing_top']['children']['listing_massaction']['children'])
            && isset($result['children']['sales_order_grid_data_source'])
        ) {
            $children = &$result['children']['listing_top']['children']['listing_massaction']['children'];
            $this->availableActions = explode(',', (string)$this->helper->getModuleConfig('general/commands'));

            foreach ($children as $item) {
                $actionName = $item['attributes']['name'];

                if (!$this->isActionAvailable($actionName)) {
                    unset($children[$actionName]);
                    continue;
                }

                if ($actionName == self::ACTION_STATUS) {
                    $children[self::ACTION_STATUS] = $this->addStatusValues($children['amasty_oaction_status']);
                }
            }

            // phpcs:ignore: Generic.Files.LineLength.TooLong
            $component = &$result['children']['listing_top']['children']['listing_massaction']['arguments']['data']['item']['config']['item']['component']['value'];

            if ($component !== self::UI_COMPONENT) {
                $component = self::UI_COMPONENT;
            }
        }

        return $result;
    }

    /**
     * @param string $actionName
     *
     * @return bool
     */
    private function isActionAvailable(string $actionName): bool
    {
        if ($actionName == self::ACTION_DELIMITER
            || strpos($actionName, self::ACTION_SIGN) === false
        ) {
            return true;
        }

        if (in_array($actionName, $this->availableActions)) {
            if ($actionName == self::ACTION_ORDER_ATTRIBUTES) {
                if (!$this->moduleManager->isEnabled(OrderAttributesChecker::AMASTY_ORDER_ATTRIBUTES_MODULE_NAME)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @param array $item
     *
     * @return array
     */
    private function addStatusValues(array $item): array
    {
        $childItem = [];
        $i = 0;
        $excludedStatuses = explode(',', (string)$this->helper->getModuleConfig('status/exclude_statuses'));
        $statuses = $this->orderStatus->toOptionArray();
        array_unshift($statuses, [
            'value' => '',
            'label' => __('Magento default')->render()
        ]);

        foreach ($statuses as $status) {
            if (!in_array($status['value'], $excludedStatuses) || $status['value'] == '') {
                $childItem[] = [
                    "name" => (string)$i++,
                    "xsi:type" => "array",
                    "item" => [
                        "label" => [
                            "name" => "label",
                            "xsi:type" => "string",
                            "value" => $status['label']
                        ],
                        "fieldvalue" => [
                            "name" => "fieldvalue",
                            "xsi:type" => "string",
                            "value" => $status['value']
                        ],
                    ]
                ];
            }
        }

        $item['arguments']['actions']['item'][0]['item']['child'] = [
            "name" => "child",
            "xsi:type" => "array",
            'item' => $childItem
        ];

        return $item;
    }
}
