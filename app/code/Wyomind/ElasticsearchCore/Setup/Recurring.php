<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Setup;

class Recurring implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @var \Symfony\Component\Console\Output\ConsoleOutput
     */
    public $output = null;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    protected $_directoryList = null;
    
    /**
     * @var string
     */
    public $magentoVersion = '';
    
    /**
     * @param \Symfony\Component\Console\Output\ConsoleOutput $output
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\App\ProductMetadata $productMetaData
     */
    public function __construct(
        \Symfony\Component\Console\Output\ConsoleOutput $output,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ProductMetadata $productMetaData
    )
    {
        $this->output = $output;
        $this->_directoryList = $directoryList;
        $explodedVersion = explode('-', $productMetaData->getVersion()); // remove all after "-" (eg: 2.2.3-beta => 2.2.3)
        $this->magentoVersion = $explodedVersion[0];
    }
    
    /**
     * {@inheritdoc}
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    )
    {
        $this->copyAutocompleteFile();
        $this->copyFilesByMagentoVersion();
    }

    public function copyAutocompleteFile()
    {
        $this->output->writeln('');
        $this->output->writeln('<comment>Copying the elastic.php file</comment>');

        $path = str_replace('Setup' . DIRECTORY_SEPARATOR . 'Recurring.php', '', __FILE__) ;
        $file = 'elastic.php';
        $rootPath = $this->_directoryList->getRoot();

        copy($path . DIRECTORY_SEPARATOR . $file, $rootPath . DIRECTORY_SEPARATOR . $file);
        copy($path . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . $file, $rootPath . DIRECTORY_SEPARATOR . 'pub' . DIRECTORY_SEPARATOR . $file);
    }

    public function copyFilesByMagentoVersion()
    {
        $files = [
            'Block/Product/Renderer/Configurable.php',
            'Model/Product/Type/Configurable.php',
            'Ui/Model/Manager.php',
            'etc/adminhtml/di.xml'
        ];

        $this->output->writeln('');
        $version = $this->magentoVersion;
        $this->output->writeln('<comment>Copying files for Magento ' . $version . '</comment>');

        $explodedVersion = explode('.', $version);
        $possibleVersion = [
            $version,
            $explodedVersion[0] . '.' . $explodedVersion[1],
            $explodedVersion[0]
        ];

        $path = str_replace('Setup' . DIRECTORY_SEPARATOR . 'Recurring.php', '', __FILE__);

        foreach ($files as $file) {
            $fullFile = $path . str_replace('/', DIRECTORY_SEPARATOR, $file);
            $ext = pathinfo($fullFile, PATHINFO_EXTENSION);

            foreach ($possibleVersion as $v) {
                $newFile = str_replace('.' . $ext, '_' . $v . '.' . $ext, $fullFile);
                if (file_exists($newFile)) {
                    copy($newFile, $fullFile);
                    break;
                }
            }
        }
    }
}