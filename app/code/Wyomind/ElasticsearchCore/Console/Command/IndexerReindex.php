<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Console\Command;

/**
 * $ bin/magento help wyomind:elasticsearchcore:indexer:reindex
 * Usage:
 * wyomind:elasticsearchcore:indexer:reindex [<index>]
 */
class IndexerReindex extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state = null;

    /**
     * @var \Wyomind\ElasticsearchCore\Helper\IndexerFactory
     */
    protected $_indexerHelperFactory = null;

    /**
     * @param \Magento\Framework\App\State $state
     * @param \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
     */
    public function __construct(
        \Magento\Framework\App\State $state,
        \Wyomind\ElasticsearchCore\Helper\IndexerFactory $indexerHelperFactory
    )
    {
        $this->_state = $state;
        $this->_indexerHelperFactory = $indexerHelperFactory;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('wyomind:elasticsearchcore:indexer:reindex')
            ->setDescription(__('Reindexes Wyomind ElasticsearchCore Data'))
            ->setDefinition([
                new \Symfony\Component\Console\Input\InputArgument(
                    'index',
                    \Symfony\Component\Console\Input\InputArgument::OPTIONAL | \Symfony\Component\Console\Input\InputArgument::IS_ARRAY,
                    __('Space-separated list of index types or omit to apply to all indexes')
                ),
                new \Symfony\Component\Console\Input\InputOption(
                    'store',
                    's',
                    \Symfony\Component\Console\Input\InputOption::VALUE_OPTIONAL,
                    __('Code of the storeview for which to re-index data')
                )
            ]);
        parent::configure();
    }

    /**
     * Executes the current command
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

        $indexers = $this->getIndexers($input);
        $storeCode = $input->getOption('store');

        foreach ($indexers as $indexerType => $indexer) {
            try {
                $startTime = microtime(true);
                $report = $indexer->reindex($indexerType, $storeCode);
                $resultTime = microtime(true) - $startTime;
                $output->writeln($report);
                $output->writeln($indexerType . " " . __("index has been rebuilt successfully in ") . gmdate('H:i:s', $resultTime));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $output->writeln($e->getMessage());
                $returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
                break;
            } catch (\Exception $e) {
                $output->writeln($indexerType . __(' indexer process unknown error:'));
                $output->writeln($e->getMessage());
                break;
            }
        }

        return $returnValue;
    }

    /**
     * Returns the ordered list of specified indexers or all indexers
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @return array $indexers
     * @throws \InvalidArgumentException
     */
    protected function getIndexers($input)
    {
        $requestedTypes = [];
        $allIndexers = $this->_indexerHelperFactory->create()->getAllIndexers();
        $arguments = $input->getArgument('index');

        if ($arguments) {
            $requestedTypes = array_filter(array_map('trim', $arguments), 'strlen');
        }

        if (empty($requestedTypes)) {
            $indexers = $allIndexers;
        } else {
            $indexers = array_intersect_key($allIndexers, array_flip($requestedTypes));

            if (empty($indexers)) {
                throw new \InvalidArgumentException(
                    "The following requested index types are not supported: '" . join("', '", $requestedTypes)
                    . "'." . PHP_EOL . 'Supported types: ' . join(', ', array_keys($allIndexers))
                );
            }
        }

        return $indexers;
    }
}