<?php
namespace Custom\City\Controller\Adminhtml\Zip;
use Custom\City\Controller\Adminhtml\Zip;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\Response\Http\FileFactory;
class Import extends Zip
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->downloadSampleZipsAction();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::import_zip');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Zip Codes'));

        return $resultPage;
    }

    /**
     * Sample dowanload link
     */
    public function downloadSampleZipsAction(){
        $dir = $this->directoryList;
        // let's get the log dir for instance
        $logDir = $dir->getPath(DirectoryList::VAR_DIR);

        $path = $logDir . '/' .'import';
        if (!file_exists($path)) {
            mkdir($path,0777,true);
        }
        $htaccess_file = $path.'/.htaccess';
        if (!file_exists($htaccess_file)) {
            $text = "allow from all";
            // Write the contents back to the file
            file_put_contents($htaccess_file, $text);
        }
        //Sample Csv generation Code
        $outputFile = $logDir . '/' .'import' . '/' .'zipsimport.csv';
        if (!file_exists($outputFile)) {
            $heading = [
                __('Zipcode'),
                __('Zipcode Ar'),
                __('City')
            ];
            $handle = fopen($outputFile, 'w');
            fputcsv($handle, $heading);

        }
    }
}