<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

class Log
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder = null;

    /**
     * @var Config
     */
    protected $_configHelper = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Logger\LoggerReindex
     */
    protected $_loggerReindex = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Logger\LoggerServer
     */
    protected $_loggerServer = null;


    private $_logger = null;

    /**
     * @var boolean
     */
    private $_logEnabled = false;

    /**
     * Log constructor
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param Config $configHelper
     * @param \Wyomind\ElasticsearchCore\Logger\LoggerReindex $loggerReindex
     * @param \Wyomind\ElasticsearchCore\Logger\LoggerServer $loggerServer
     */
    public function __construct(
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        Config $configHelper,
        \Wyomind\ElasticsearchCore\Logger\LoggerReindex $loggerReindex,
        \Wyomind\ElasticsearchCore\Logger\LoggerServer $loggerServer
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->_configHelper = $configHelper;
        $this->_loggerReindex = $loggerReindex;
        $this->_loggerServer = $loggerServer;
    }

    public function log($message)
    {
        // Check if log is enables
        $this->checkLogFlag();

        $this->__log($message);
    }

    /**
     * Log the server failure - send a notification email
     * @param string $storeId
     * @param string $message
     * @param int $serverStatus
     * @param string $serverVersion
     * @param int $compatibility
     */
    public function serverLog($storeId, $message, $serverStatus = 0, $serverVersion = null, $compatibility = 6)
    {
        // Log only when the server status change
        if ($serverStatus != $this->_configHelper->getServerStatus($storeId)) {
            // Check if log is enables
            $this->checkLogFlag('server');

            $this->__log("************* ELASTICSEARCH SERVER STATUS " . ($serverStatus ? "RESUMED" : "FAILED") . " *************");
            $this->__log('Store ID : ' . $storeId);
            $this->__log('Server version : ' . $serverVersion);
            $this->__log('Compatibility : ' . $compatibility);
            $this->__log('Server Status : ' . $serverStatus);
            $this->__log('Message : ' . $message);

            // Email notification when the server failed
            if ($this->_configHelper->isServerStatusMailNotificationEnabled()) {
                $emails = explode(',', $this->_configHelper->getServerStatusMailNotificationEmails());

                if (count($emails) > 0) {
                    try {
                        $template = Config::MAIL_NOTIFICATION_TEMPLATE;
                        $subject = $this->_configHelper->getServerStatusMailNotificationSubject();
                        $configContent = $this->_configHelper->getServerStatusMailNotificationContent();

                        $content = str_replace(
                            ['{{store_id}}', '{{message}}', '{{server_status}}', '{{server_version}}'],
                            [$storeId, $message, ($serverStatus ? "resumed" : "failed"), $serverVersion],
                            $configContent
                        );

                        $senderEmail = $this->_configHelper->getServerStatusMailNotificationSenderMail();
                        $senderName = $this->_configHelper->getServerStatusMailNotificationSenderName();

                        $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                                                            ->setTemplateOptions([
                                                                'area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
                                                                'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                                                            ])
                                                            ->setTemplateVars(['content' => $content, 'subject' => $subject])
                                                            ->setFrom(['email' => $senderEmail, 'name' => $senderName])
                                                            ->addTo($emails[0]);
                        $count = count($emails);
                        for ($i = 1; $i < $count; $i++) {
                            $transport->addCc($emails[$i]);
                        }
                        $transport->getTransport()->sendMessage();
                    } catch (\Exception $e) {
                        $this->__log('>> EMAIL ERROR! ' . $e->getMessage());
                    }
                }
            }
        }
    }

    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /*                        DEBUG UTILITIES                        */
    /* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
    /**
     * Check if the log reporting is enabled
     * - reindex: log the reindex process [default]
     * - server: log the server status
     * @var string $type
     */
    private function checkLogFlag($type = 'reindex')
    {
        switch ($type) {
            case 'server':
                $method = 'isServerStatusLogEnabled';
                $this->_logger = $this->_loggerServer;
                break;
            case 'reindex':
            default:
                $method = 'isReindexLogEnabled';
                $this->_logger = $this->_loggerReindex;
        }

        if (!$this->_logEnabled) {
            $this->_logEnabled = ($this->_configHelper->{$method}()) ? true : false;
        }
    }

    /**
     * Add a message to the log file
     * @param string $message
     */
    public function __log($message)
    {
        if ($this->_logEnabled) {
            $this->_logger->notice($message);
        }
    }
}