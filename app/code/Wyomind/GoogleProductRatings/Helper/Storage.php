<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Helper;

class Storage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\FileSystem\DirectoryList
     */
    protected $_directoryList;
    /**
     * @var \Magento\Framework\Filesystem\Driver\FileFactory
     */
    protected $_driverFileFactory;
    /**
     * Class constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\FileSystem\DirectoryList $_directoryList
     * @param \Magento\Framework\Filesystem\Driver\FileFactory $_driverFileFactory
     */
    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\App\FileSystem\DirectoryList $_directoryList, \Magento\Framework\FileSystem\Driver\FileFactory $_driverFileFactory)
    {
        $this->_directoryList = $_directoryList;
        $this->_driverFileFactory = $_driverFileFactory;
        parent::__construct($context);
    }
    /**
     * Gets path of the Magento root directory
     *
     * @return string
     */
    public function getAbsoluteRootDir()
    {
        return $this->_directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::ROOT);
    }
    /**
     * Driver file instance
     *
     * @return \Magento\Framework\Filesystem\Driver\File
     */
    public function getDriverFile()
    {
        return $this->_driverFileFactory->create();
    }
    /**
     * Create directory
     *
     * @param string $folder
     * @return boolean
     */
    public function mkdir($folder)
    {
        return $this->getDriverFile()->createDirectory($folder, 0755);
    }
    /**
     * Open file
     *
     * @param string $folder
     * @param string $fileName
     * @param string $mode
     * @return resource file $resource
     */
    public function fileOpen($folder, $fileName, $mode = 'w')
    {
        $resource = $this->getDriverFile()->fileOpen($folder . DIRECTORY_SEPARATOR . $fileName, $mode);
        return $resource;
    }
    /**
     * Writes data to file
     *
     * @param resource $resource
     * @param string $data
     * @return int
     */
    public function fileWrite($resource, $data)
    {
        return $this->getDriverFile()->fileWrite($resource, $data);
    }
    /**
     * Close file
     *
     * @param resource $resource
     * @return boolean
     */
    public function fileClose($resource)
    {
        return $this->getDriverFile()->fileClose($resource);
    }
    /**
     * Is file exist in file system
     *
     * @param string $folder
     * @param string $fileName
     * @return boolean
     */
    public function fileExists($folder, $fileName)
    {
        return $this->getDriverFile()->isExists($folder . DIRECTORY_SEPARATOR . $fileName);
    }
}