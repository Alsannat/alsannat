<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Model;

class CsvImportHandler
{
    /**
    * CSV Processor
    *
    * @var \Magento\Framework\File\Csv
    */
    protected $csvProcessor;
     
    public function __construct(
         \Magento\Framework\File\Csv $csvProcessor
    )
    {
         $this->csvProcessor = $csvProcessor;
    }

    /**
    * This functiomn will prforem bulk Naqel cities upload
    */
    public function importFromCsvFile($file)
    {
         if (!isset($file['tmp_name'])) {
             throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
         }
         $importProductRawData = $this->csvProcessor->getData($file['tmp_name']);
     
         foreach ($importProductRawData as $rowIndex => $dataRow) 
         {
             \Zend_Debug::dump($dataRow);
         }
         die();
    }
}