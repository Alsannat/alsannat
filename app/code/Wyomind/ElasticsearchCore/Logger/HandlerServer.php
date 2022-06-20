<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\ElasticsearchCore\Logger;

/**
 * Log handler for Wyomind_Core
 */
class HandlerServer extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * The log file name
     * @var string
     */
    public $fileName = '/var/log/Wyomind_ElasticsearchCore_Server_Status.log';

    /**
     * The log level
     * @var integer
     */
    public $loggerType = \Monolog\Logger::NOTICE;
}