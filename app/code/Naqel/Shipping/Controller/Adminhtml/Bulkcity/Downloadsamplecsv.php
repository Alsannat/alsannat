<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Bulkcity;

use Magento\Framework\App\Filesystem\DirectoryList;

class Downloadsamplecsv extends \Magento\Backend\App\Action
{
    protected $_filesystem;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem
    ) 
    {
        parent::__construct($context);
        $this->_filesystem = $filesystem;
    }
    
    public function execute()
    {
        $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath();

        $sample_File_Path = $mediapath.'code/Naqel/Shipping/sample/naqel_city.csv';
        
        header("Content-disposition: attachment; filename=sample_naqel_city.csv");
        header("Content-type: application/csv");
        print_r(readfile($sample_File_Path));
        exit();
    }
}
