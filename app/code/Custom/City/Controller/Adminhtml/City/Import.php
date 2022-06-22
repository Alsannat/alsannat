<?php

namespace Custom\City\Controller\Adminhtml\City;
use Custom\City\Controller\Adminhtml\City;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\App\Response\Http\FileFactory;
class Import extends City
{

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->downloadSampleZipsAction();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_City::city_import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import Cities'));
        return $resultPage;
    }
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
        $outputFile = $logDir . '/' .'import' . '/' .'citiesimport.csv';
        if (!file_exists($outputFile)) {
            $heading = [
                __('City'),
                __('State'),
                __('City_ar')
            ];
            $handle = fopen($outputFile, 'w');
            fputcsv($handle, $heading);

        }
    }
}