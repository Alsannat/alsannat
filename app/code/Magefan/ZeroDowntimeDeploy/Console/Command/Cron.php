<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
declare(strict_types=1);

namespace Magefan\ZeroDowntimeDeploy\Console\Command;

use Magefan\ZeroDowntimeDeploy\Model\Deploy;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magefan\ZeroDowntimeDeploy\Model\Config;
use Magento\Framework\Filesystem\Io\File;

class Cron extends Command
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Deploy
     */
    private $deploy;

    /**
     * @var File
     */
    private $file;

    /**
     * Create constructor.
     * @param Config $config
     * @param Deploy $deploy
     * @param File $file
     * @param string|null $name
     */
    public function __construct(
        Config $config,
        Deploy $deploy,
        File $file,
        string $name = null
    ) {
        $this->config = $config;
        $this->deploy = $deploy;
        $this->file = $file;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    public function configure()
    {
        $this->setName('magefan:zero-downtime:cron');
        $this->setDescription('Execute Cron');

        parent::configure();
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->isEnabled()) {
            if ($this->file->fileExists(Config::RUNNING_FLAG_FILE)) {
                return;
            }

            if ($this->file->fileExists(Config::SCHEDULED_FLAG_FILE)) {
                $this->file->rm(Config::SCHEDULED_FLAG_FILE);
                $this->deploy->execute($input, $output);
            }
        } else {
            $output->writeln('<comment>'
                . __(strrev('sdnammoC yolpeD nuR oT emitnwoD oreZ >- snoisnetxE nafegaM >- noitarugifnoC >- 
                erotS nI noisnetxE elbanE esaelP'))
                .'</comment>');
        }
    }
}
