<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Cron;

class Run
{
    /**
     * @var array
     */
    private $_log = [];
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind)
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
    }
    public function run(\Magento\Cron\Model\Schedule $schedule)
    {
        try {
            $this->_logger->notice("---------------------------- CRON PROCESS ----------------------------");
            $this->_log[] = "---------------------------- CRON PROCESS ----------------------------";
            // Run the generation for each website and storeview
            $websites = $this->_websiteRepository->getList();
            $stores = $this->_storeRepository->getList();
            foreach ($websites as $website) {
                $this->execute('website', $website);
            }
            foreach ($stores as $store) {
                // Check if the scope is active
                if ($store->isActive()) {
                    $this->execute('store', $store);
                }
            }
            // Cron reporting enabled
            $reporting = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_CRON_JOB_ENABLE_REPORTING);
            if ($reporting) {
                $emails = explode(',', $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_CRON_JOB_REPORTING_SETTINGS_EMAILS));
                if (count($emails) > 0) {
                    // Send email
                    try {
                        $template = \Wyomind\GoogleProductRatings\Helper\Config::MAIL_NOTIFICATION_TEMPLATE;
                        $senderName = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_CRON_JOB_REPORTING_SETTINGS_SENDER_EMAIL);
                        $senderEmail = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_CRON_JOB_REPORTING_SETTINGS_SENDER_NAME);
                        $subject = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_CRON_JOB_REPORTING_SETTINGS_SUBJECT);
                        $transport = $this->_transportBuilder->setTemplateIdentifier($template)->setTemplateOptions(['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE, 'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID])->setTemplateVars(['report' => implode("<br/>", $this->_log), 'subject' => $subject])->setFrom(['email' => $senderEmail, 'name' => $senderName])->addTo($emails[0]);
                        $count = count($emails);
                        for ($i = 1; $i < $count; $i++) {
                            $transport->addCc($emails[$i]);
                        }
                        $transport->getTransport()->sendMessage();
                    } catch (\Exception $e) {
                        $this->_logger->notice('>> EMAIL ERROR! ' . $e->getMessage());
                        $log[] = '>> EMAIL ERROR! ' . $e->getMessage();
                    }
                }
            }
        } catch (\Exception $e) {
            $schedule->setStatus('failed');
            $schedule->setMessage($e->getMessage());
            $schedule->save();
            $this->_logger->notice(">> MASSIVE ERROR ! ");
            $this->_logger->notice($e->getMessage());
        }
    }
    /**
     * Execute and log the data feed generation
     *
     * @param string $type
     * @param Object $scope
     */
    private function execute($type, $scope)
    {
        $scopeId = $scope->getId();
        $done = false;
        $this->_logger->notice(">> Cron run feed generation for " . $type . " = " . $scopeId);
        $this->_log[] = ">> Cron run feed generation for " . $type . " = " . $scopeId;
        try {
            $configUpdatedAt = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_STORAGE_UPDATED_AT, $scopeId);
            $updatedAt = $configUpdatedAt ? $configUpdatedAt : 1;
            $cron = [];
            $cron['curent']['localDate'] = $this->_coreDate->date('l Y-m-d H:i:s');
            $cron['curent']['gmtDate'] = $this->_coreDate->gmtDate('l Y-m-d H:i:s');
            $cron['curent']['localTime'] = $this->_coreDate->timestamp();
            $cron['curent']['gmtTime'] = $this->_coreDate->gmtTimestamp();
            $cron['file']['localDate'] = $this->_coreDate->date('l Y-m-d H:i:s', $updatedAt);
            $cron['file']['gmtDate'] = $this->_coreDate->gmtDate('l Y-m-d H:i:s', $updatedAt);
            $cron['file']['localTime'] = $this->_coreDate->timestamp($updatedAt);
            $cron['file']['gmtTime'] = $updatedAt;
            $cron['offset'] = $this->_coreDate->getGmtOffset('hours');
            $this->_logger->notice('>> Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localDate'] . ' GMT' . $cron['offset']);
            $this->_log[] = '>> Last update : ' . $cron['file']['gmtDate'] . " GMT / " . $cron['file']['localDate'] . ' GMT' . $cron['offset'] . "";
            $this->_logger->notice('>> Current date : ' . $cron['curent']['gmtDate'] . " GMT / " . $cron['curent']['localDate'] . ' GMT' . $cron['offset']);
            $this->_log[] = '>> Current date : ' . $cron['curent']['gmtDate'] . " GMT / " . $cron['curent']['localDate'] . ' GMT' . $cron['offset'] . "";
            $configCron = $this->_framework->getStoreConfig(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_SCHEDULE_CRON, $scopeId);
            $cronExpr = json_decode($configCron);
            $i = 0;
            if ($cronExpr != null && isset($cronExpr->days)) {
                foreach ($cronExpr->days as $d) {
                    foreach ($cronExpr->hours as $h) {
                        $time = explode(':', $h);
                        if (date('l', $cron['curent']['gmtTime']) == $d) {
                            $cron['tasks'][$i]['localTime'] = strtotime($this->_coreDate->date('Y-m-d')) + $time[0] * 60 * 60 + $time[1] * 60;
                            $cron['tasks'][$i]['localDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                        } else {
                            $cron['tasks'][$i]['localTime'] = strtotime("last " . $d, $cron['curent']['localTime']) + $time[0] * 60 * 60 + $time[1] * 60;
                            $cron['tasks'][$i]['localDate'] = date('l Y-m-d H:i:s', $cron['tasks'][$i]['localTime']);
                        }
                        if ($cron['tasks'][$i]['localTime'] >= $cron['file']['localTime'] && $cron['tasks'][$i]['localTime'] <= $cron['curent']['localTime'] && $done != true) {
                            $this->_logger->notice('>> Scheduled : ' . ($cron['tasks'][$i]['localDate'] . " GMT" . $cron['offset']));
                            $this->_log[] = '>> Scheduled : ' . ($cron['tasks'][$i]['localDate'] . " GMT" . $cron['offset']) . "";
                            $this->_logger->notice('>> Starting generation');
                            $scopeIds = [$type => $scopeId];
                            $data = $this->_feedHelper->generate($scopeIds);
                            $done = true;
                            $this->_logger->notice(">> Link: " . $data['link']);
                            $this->_log[] = ">> Link: " . $data['link'];
                            $this->_logger->notice(">> EXECUTED!");
                            $this->_log[] = ">> EXECUTED!";
                        }
                        $i++;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_logger->notice(">> ERROR! " . $e->getMessage());
            $this->_log[] = ">> ERROR! " . $e->getMessage() . "";
        }
        if (!$done) {
            $this->_logger->notice(">> SKIPPED!");
            $this->_log[] = ">> SKIPPED!";
        }
    }
}