<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Logger;

/**
 * Log handler for Wyomind_Framework
 */
class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * The log file name
     * @var string
     */
    public $fileName = '/var/log/GoogleProductRatings.log';

    /**
     * The log level
     * @var integer
     */
    public $loggerType = \Monolog\Logger::NOTICE;
}
