<?php
/**
 * Created by PhpStorm.
 * User: joel
 * Date: 23/04/2017
 * Time: 14:37
 */
namespace Magenest\GiftCard\Logger;

use Magento\Framework\Logger\Handler\Base;

class Handler extends Base
{
    protected $fileName = '/var/log/giftcard/debug.log';
    protected $loggerType = \Monolog\Logger::DEBUG;
}