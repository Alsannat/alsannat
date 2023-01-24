<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Logger;

class HandlerCron extends \Magento\Framework\Logger\Handler\Base
{
    public $fileName = '/var/log/GoogleProductRatings-cron.log';
    public $loggerType = \Monolog\Logger::NOTICE;
}
