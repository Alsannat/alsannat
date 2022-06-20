<?php
namespace Evincemage\Subscribers\Console\Command;

USE Magento\Framework\App\State;
use Magento\Store\Model\StoreManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManagerFactory;
use Symfony\Component\Console\Input\InputOption;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Newsletter\Model\Subscriber;

/**
 * Class SubscriberImportCommand
 * @package MYNAMESPACE\MYMODULE\Console\Command
 */
class SubscriberImportCommand extends \Symfony\Component\Console\Command\Command
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * @var Magento\Newsletter\Model\SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     *
     */
    const CSV_EMAIL_POSITION = 5;

    /**
     * SubscriberImportCommand constructor.
     *
     * @param ObjectManagerFactory $objectManagerFactory
     * @param State $state
     * @param DirectoryList $_directoryList
     * @param SubscriberFactory $_subscriberFactory
     */
    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        State $state,
        DirectoryList $_directoryList,
        SubscriberFactory $_subscriberFactory
    ){
        $params = $_SERVER;

        $params[StoreManager::PARAM_RUN_CODE] = 'admin';
        $params[StoreManager::PARAM_RUN_TYPE] = 'store';
        //$this->_objectManager = $objectManagerFactory->create($params);
        //$state->setAreaCode('adminhtml');


        $this->_directoryList = $_directoryList;
        $this->_subscriberFactory = $_subscriberFactory;
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('newsletter:subscriber:import')
            ->setDescription('Imports newsletter subscribers');

        $this->setDefinition([new InputOption(
            'csv-path',
            null,
            InputOption::VALUE_REQUIRED,
            'Path to csv file within Magento.',
            null
        )]);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return bool
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Starting Category Cleanup</info>');

        $magentoRoot = $this->_directoryList->getPath(DirectoryList::ROOT);
        $csvPath = $magentoRoot . '/' . $input->getOption('csv-path');

        if(!file_exists($csvPath)) {
            $output->writeln("<error>CVS file not found in {$csvPath}</error>");
            return false;
        }

        $csv = array_map('str_getcsv', file($csvPath));
        $count = count($csv);

        foreach($csv as $key => $row) {
            $email = $row[self::CSV_EMAIL_POSITION];

            if(!$this->isValidEmail($email)) {
                continue 1;
            }

            $this->_subscriberFactory->create()
                ->setStatus(Subscriber::STATUS_SUBSCRIBED)
                ->setEmail($email)
                ->save();

            $output->writeln("<info>{$key} of {$count} - {$email} has been added.</info>");
        }
        return true;
    }

    /**
     * Remove all illegal characters from email and validates it.
     *
     * @param string $email
     * @return bool
     */
    protected function isValidEmail(string $email)
    {
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return false;
        }

        return true;
    }
}
