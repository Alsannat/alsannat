<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Block\Adminhtml\System\Config\Form\Field;

class Cron extends \Magento\Config\Block\System\Config\Form\Field
{
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        parent::__construct($context, $data);
    }
    /**
     * Return HTML element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $message = $this->_cronHelper->checkHeartBeat();
        $html = '<div id="messages"><ul class="messages">' . $message . '</ul></div>' . '<div id="cron-setting">' . '<input class="input-text" type="hidden" id="cron_expr" ' . 'name="' . $element->getName() . '" value="' . $element->getEscapedValue() . '" />';
        $hoursLines = '';
        $daysLines = '';
        $days = [__('Tuesday'), __('Wednesday'), __('Thursday'), __('Friday'), __('Saturday'), __('Sunday')];
        foreach ($days as $day) {
            $daysLines .= '<tr>' . '<td class="cron-d-box">' . '<label class="data-grid-checkbox-cell-inner" for="d-' . $day . '">' . '<input class="cron-box day" value="' . $day . '" id="d-' . $day . '" type="checkbox"/>' . '&nbsp;' . __($day) . '</label>' . '</td>' . '</tr>';
        }
        for ($hour = 0; $hour < 12; $hour++) {
            $am = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $pm = $hour + 12;
            $hoursLines .= '<tr>' . '<td class="cron-h-box">' . '<label class="data-grid-checkbox-cell-inner" for="h-' . $am . '00">' . '<input class="cron-box hour" value="' . $am . ':00" id="h-' . $am . '00" type="checkbox" />' . '&nbsp;' . $am . ':00 AM' . '</label>' . '</td>' . '<td class="cron-h-box">' . '<label class="data-grid-checkbox-cell-inner" for="h-' . $am . '30">' . '<input class="cron-box hour" value="' . $am . ':30" id="h-' . $am . '30" type="checkbox" />' . '&nbsp;' . $am . ':30 AM' . '</label>' . '</td>' . '<td class="cron-h-box">' . '<label class="data-grid-checkbox-cell-inner" for="h-' . $pm . '00">' . '<input class="cron-box hour" value="' . $pm . ':00" id="h-' . $pm . '00" type="checkbox" />' . '&nbsp;' . $pm . ':00 PM' . '</label>' . '</td>' . '<td class="cron-h-box">' . '<label class="data-grid-checkbox-cell-inner" for="h-' . $pm . '30">' . '<input class="cron-box hour" value="' . $pm . ':30" id="h-' . $pm . '30" type="checkbox" />' . '&nbsp;' . $pm . ':30 PM' . '</label>' . '</td>' . '</tr>';
        }
        $html .= '<table>' . '<thead>' . '<tr>' . '<th>' . __('Days of the week') . '</th>' . '<th colspan="4">' . __('Hours of the day') . '</th>' . '</tr>' . '</thead>' . '<tbody>' . '<tr>' . '<td class="cron-d-box">' . '<label class="data-grid-checkbox-cell-inner" for="d-Monday">' . '<input class="cron-box day" value="Monday" id="d-Monday" type="checkbox"/>' . '&nbsp;' . __('Monday') . '</label>' . '</td>' . '<td rowspan="7" class="hours">' . '<table>' . $hoursLines . '</table>' . '</td>' . $daysLines . '</tr>' . '</tbody>' . '</table>' . '<script>' . 'require(["jquery", "gpr_cron"], function ($, cron) {' . '"use strict";' . '$(document).on("change", ".cron-box", function () {' . '$(this).parent().toggleClass("selected");' . 'cron.updateExpr();' . '});' . 'cron.loadExpr();' . '});' . '</script>' . '</div>';
        return $html;
    }
}