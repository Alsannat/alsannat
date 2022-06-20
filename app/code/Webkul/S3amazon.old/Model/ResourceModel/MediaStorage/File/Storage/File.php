<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_S3amazon
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\S3amazon\Model\ResourceModel\MediaStorage\File\Storage;

use Magento\Framework\Filesystem;
use Magento\MediaStorage\Helper\File\Storage\Database;

class File extends \Magento\MediaStorage\Model\ResourceModel\File\Storage\File
{
    /**
     * @var Database
     */
    protected $fileStorageDb;

    /**
     * @param Filesystem $filesystem
     * @param \Psr\Log\LoggerInterface $log
     * @param Database $fileStorageDb
     */
    public function __construct(
        Filesystem $filesystem,
        \Psr\Log\LoggerInterface $log,
        Database $fileStorageDb
    ) {
        parent::__construct($filesystem, $log);
        $this->fileStorageDb = $fileStorageDb;
    }

    /**
     * during save file uploading the file to S3 server.
     *
     * @param string $filePath
     * @param string $content
     * @param bool $overwrite
     * @return bool
     */
    public function saveFile($filePath, $content, $overwrite = false)
    {
        $result = parent::saveFile($filePath, $content, $overwrite);

        if ($result) {
            $this->fileStorageDb->saveFile($filePath);
        }

        return $result;
    }
}
