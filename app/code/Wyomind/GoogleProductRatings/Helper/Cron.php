<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Helper;

class Cron
{
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    /**
     * Check the cron heartbeat :
     * - displays an error if no cron task detected
     * - displays a simple notification if cron task detected
     * @return string
     */
    public function checkHeartbeat()
    {
        $lastHeartbeat = $this->_framework->getLastHeartbeat();
        if ($lastHeartbeat === false) {
            // no cron task found
            $message = __('No cron task found. <a href="https://www.wyomind.com/magento2/cron-scheduler-magento.html" target="_blank">Check if cron is configured correctly.</a>');
            $class = 'error';
        } else {
            $timespan = $this->_framework->dateDiff($lastHeartbeat);
            if ($timespan <= 5 * 60) {
                // everything ok
                $message = __('Scheduler is working. (Last cron task: %1 minute(s) ago)', round($timespan / 60));
                $class = 'success';
            } elseif ($timespan > 5 * 60 && $timespan <= 60 * 60) {
                // cron task wasn't executed in the last 5 minutes. Heartbeat schedule could have been modified to not run every five minutes!
                $message = __('Last cron task is older than %1 minutes.', round($timespan / 60));
                $class = 'notice';
            } else {
                $message = __('Last cron task is older than one hour. Please check your settings and your configuration!');
                $class = 'error';
            }
        }
        $notification = '<div class="message message-' . $class . ' ' . $class . '">' . $message . '</div>';
        return $notification;
    }
}