<?php
 
namespace Ced\City\Model\ResourceModel;
 
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Filesystem\DirectoryList;
 
class City extends AbstractDb
{
    protected $_importErrors = [];

    protected $_importedRows = 0;

    protected $_logger;

    protected $_filesystem;

    protected function _construct()
    {
        $this->_init('ced_cities', 'id');
    }

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        $connectionName = null
    ) {

        $this->_logger = $logger;
        $this->_filesystem = $filesystem;
        parent::__construct($context, $connectionName);
    }

    public function uploadAndImport(\Magento\Framework\DataObject $object)
    {
        $connection=$this->getConnection();
        $connection->beginTransaction();
        $connection->delete($this->getMainTable());
        $connection->commit();
        if (empty($_FILES['groups']['tmp_name']['general']['fields']['import']['value'])) {
            return $this;
        }
        $csvFile = $_FILES['groups']['tmp_name']['general']['fields']['import']['value'];
        $this->_importedRows = 0;

        $tmpDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::SYS_TMP);

        $path = $tmpDirectory->getRelativePath($csvFile);

        $stream = $tmpDirectory->openFile($path);
        // check and skip headers
        $headers = $stream->readCsv();

        $connection = $this->getConnection();
        $connection->beginTransaction();

        try {
            $rowNumber = 1;
            $importData = [];
            while (false !== ($csvLine = $stream->readCsv())) {
                $rowNumber++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = [];
                }
            }
            $this->_saveImportData($importData);
            $stream->close();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $connection->rollback();
            $stream->close();
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
        } catch (\Exception $e) {
            print_r($e->getMessage());die;
            $connection->rollback();
            $stream->close();
            $this->_logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Something went wrong while importing Cities.')
            );
        }

        $connection->commit();

        if ($this->_importErrors) {
            $error = __(
                'We couldn\'t import this file because of these errors: %1',
                implode(" \n", $this->_importErrors)
            );
            throw new \Magento\Framework\Exception\LocalizedException($error);
        }

        return $this;
    }
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row

        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        $city=$row[1];
        $cityCode=$row[0];

        return [
            $city,$cityCode
        ];
    }
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = ['city','city_code'
            ];
            $this->getConnection()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }
}