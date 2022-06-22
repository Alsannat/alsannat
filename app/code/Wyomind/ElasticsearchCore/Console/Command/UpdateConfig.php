<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Console\Command;

/**
 * $ bin/magento help wyomind:elasticsearchcore:update:config
 * Usage:
 * wyomind:elasticsearchcore:update:config
 *
 * Options:
 * --help (-h)           Display this help message
 * --quiet (-q)          Do not output any message
 * --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 * --version (-V)        Display this application version
 * --ansi                Force ANSI output
 * --no-ansi             Disable ANSI output
 * --no-interaction (-n) Do not ask any interactive question
 */
class UpdateConfig extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var StoreManagerInterface|\Magento\Store\Model\StoreManagerInterface|null
     */
    protected $_storeManager = null;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state = null;
    /**
     * @var \Wyomind\ElasticsearchCore\Helper\JsonConfig
     */
    protected $jsonConfigHelper = null;


    /**
     * UpdateServerVersion constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\State $state
     * @param \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Wyomind\ElasticsearchCore\Helper\JsonConfig $jsonConfigHelper
    )
    {
        $this->_storeManager = $storeManager;
        $this->_state = $state;
        $this->jsonConfigHelper = $jsonConfigHelper;

        parent::__construct();
    }

    /**
     * @{inheritdoc}
     */
    protected function configure()
    {
        $this->setName('wyomind:elasticsearchcore:update:config')
            ->setDescription(__('Update the static config files'))
            ->setDefinition([]);
        parent::configure();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return boolean
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    )
    {
        $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;

        try {
            $this->_state->setAreaCode('adminhtml');
        } catch (\Exception $e) {

        }

        try {

            // Other scopes
            foreach ($this->_storeManager->getStores() as $store) {
                $output->writeln(sprintf(__("Store %s (%s)"), $store['name'], $store['code']));
                $storeCode = $store->getCode();
                $this->jsonConfigHelper->saveConfig($storeCode);
                $output->writeln(sprintf(__('<info>Generated</info>')));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }

        return $returnValue;
    }
}