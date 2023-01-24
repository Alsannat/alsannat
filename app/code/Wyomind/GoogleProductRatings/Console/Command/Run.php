<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\GoogleProductRatings\Console\Command;

/**
 * wyomind:googleproductratings:run command line
 * @version 1.0.0
 * @description <pre>
 * $ bin/magento help wyomind:googleproductratings:run
 * Usage:
 * wyomind:googleproductratings:run
 *
 * Options:
 *  --website (-w)        Website id
 *  --storeview (-s)      Storeview id
 *  --help (-h)           Display this help message
 *  --quiet (-q)          Do not output any message
 *  --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 *  --version (-V)        Display this application version
 *  --ansi                Force ANSI output
 *  --no-ansi             Disable ANSI output
 *  --no-interaction (-n) Do not ask any interactive question
 * </pre>
 */

class Run extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;
    
    /**
     * @var \Magento\Store\Model\StoreManagerFactory
     */
    protected $_storeManagerFactory;
    
    /**
     * @var \Wyomind\GoogleProductRatings\Helper\FeedFactory
     */
    protected $_feedHelperFactory;
    
    /**
     * @var \Wyomind\GoogleProductRatings\Logger\LoggerFactory
     */
    protected $_loggerFactory;

    /**
     * Command line option: --website=1 (can be a list)
     */
    const WEBSITE_OPTION = 'website';
    
    /**
     * Command line option: --storeview=1 (can be a list)
     */
    const STOREVIEW_OPTION = 'storeview';


    /**
     * Run constructor.
     * @param \Magento\Framework\App\State $_state
     * @param \Magento\Store\Model\StoreManagerFactory $_storeManagerFactory
     * @param \Wyomind\GoogleProductRatings\Helper\FeedFactory $_feedHelperFactory
     * @param \Wyomind\GoogleProductRatings\Logger\LoggerFactory $_loggerFactory
     */
    public function __construct(
        \Magento\Framework\App\State $_state,
        \Magento\Store\Model\StoreManagerFactory $_storeManagerFactory,
        \Wyomind\GoogleProductRatings\Helper\FeedFactory $_feedHelperFactory,
        \Wyomind\GoogleProductRatings\Logger\LoggerFactory $_loggerFactory
    ) {
    
        $this->_state = $_state;
        $this->_storeManagerFactory = $_storeManagerFactory;
        $this->_feedHelperFactory = $_feedHelperFactory;
        $this->_loggerFactory = $_loggerFactory;
        parent::__construct();
    }
    
    /**
     * Configure the command line
     */
    protected function configure()
    {
        $this->setName('wyomind:googleproductratings:run')
                ->setDescription(__('Google Product Ratings: generate reviews feeds'))
                ->setDefinition([
                    new \Symfony\Component\Console\Input\InputOption(
                        self::WEBSITE_OPTION,
                        'w',
                        \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED | \Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY,
                        __('Website id, -w 0 for the default config (all reviews in one file)')
                    ),
                    new \Symfony\Component\Console\Input\InputOption(
                        self::STOREVIEW_OPTION,
                        's',
                        \Symfony\Component\Console\Input\InputOption::VALUE_REQUIRED | \Symfony\Component\Console\Input\InputOption::VALUE_IS_ARRAY,
                        __('Storeview id')
                    )
                ]);
        
        parent::configure();
    }
    
    /**
     * Execute the command line
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return \Magento\Framework\Console\Cli::RETURN_FAILURE | \Magento\Framework\Console\Cli::RETURN_SUCCESS
     */
    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
    
        try {
            $this->_state->setAreaCode('adminhtml');

            $feedHelper = $this->_feedHelperFactory->create();
            $logger = $this->_loggerFactory->create();
            $storeManager = $this->_storeManagerFactory->create();

            $websiteOption = $input->getOption(self::WEBSITE_OPTION);
            $storeviewOption = $input->getOption(self::STOREVIEW_OPTION);

            $logger->notice("");
            $logger->notice("~~~~~~~~~~~~ COMMAND LINE PROCESS ~~~~~~~~~~~~~");
            
            if (empty($websiteOption) && empty($storeviewOption)) {
                // Run the generation for each website and storeview
                $websites = $storeManager->getWebsites();
                $storeviews = $storeManager->getStores();
                
                foreach ($websites as $website) {
                    $websiteOption[] = $website->getId();
                }
                
                foreach ($storeviews as $storeview) {
                    $storeviewOption[] = $storeview->getId();
                }
                
                $logger->notice("");
                $logger->notice(__("~~~ Generate feed for the default config ~~~"));
                
                $data = $feedHelper->generate(['default']);
                
                $output->writeln(sprintf(__("Default data feed generated: %1", $data['link'])));
                $logger->notice(__("~~~ File %1 ~~~", $data['link']));
            }
            
            foreach ($websiteOption as $websiteId) {
                $website = $storeManager->getWebsite($websiteId);
                $storeCode = $website->getCode();
                $logger->notice("");
                $logger->notice(__("~~~ Generate feed for website #%1 (%2) ~~~", $websiteId, $storeCode));

                $data = $feedHelper->generate(['website' => $websiteId]);

                $output->writeln(sprintf(__("Website #%1: data feed generated: %2", $websiteId, $data['link'])));
                $logger->notice(__("~~~ File %1 ~~~", $data['link']));
            }

            foreach ($storeviewOption as $storeId) {
                $store = $storeManager->getStore($storeId);
                $storeCode = $store->getCode();
                $logger->notice("");
                $logger->notice(__("~~~ Generate feed for storeview #%1 (%2) ~~~", $storeId, $storeCode));

                if ($store->isActive()) {
                    $data = $feedHelper->generate(['store' => $storeId]);

                    $output->writeln(sprintf(__("Storeview #%1: data feed generated: %2", $storeId, $data['link'])));
                    $logger->notice(__("~~~ File %1 ~~~", $data['link']));
                } else {
                    $output->writeln(sprintf(__("Storeview #%1 (%2) is disabled", $storeId, $storeCode)));
                    $logger->notice(__("~~~ Storeview #%1 (%2) is disabled ~~~", $storeId, $storeCode));
                }
            }
            
            $logger->notice("~~~~~~~~~~~~ END COMMAND LINE PROCESS ~~~~~~~~~~~~");
            
            $returnValue = \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $output->writeln($e->getMessage());
            $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
        
        return $returnValue;
    }
}
