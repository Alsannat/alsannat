<?php

namespace Custom\SpecialPrice\Controller\Adminhtml\Index;


use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Catalog\Api\SpecialPriceInterface;
use Magento\Catalog\Api\Data\SpecialPriceInterfaceFactory;


class Post extends \Magento\Backend\App\Action
{
    
    protected $resultPageFactory;
	protected $messageManager; 
 	protected $filesystem;
 	protected $fileUploader;
 	protected $csv;
 	/**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var SpecialPriceInterface
     */
    private $specialPrice;

    /**
     * @var SpecialPriceInterfaceFactory
     */
    private $specialPriceFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		ManagerInterface $messageManager,
		Filesystem $filesystem,
		UploaderFactory $fileUploader,
		\Magento\Framework\File\Csv $csv,
		StoreRepositoryInterface $storeRepository,
		SpecialPriceInterface $specialPrice,
        SpecialPriceInterfaceFactory $specialPriceFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->messageManager = $messageManager;
		$this->filesystem = $filesystem;
 		$this->fileUploader = $fileUploader;
 		$this->csv = $csv;
 		$this->storeRepository = $storeRepository;
 		$this->specialPrice = $specialPrice;
        $this->specialPriceFactory = $specialPriceFactory;
 		$this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR);
        parent::__construct($context);
    }

    public function execute()
    {
		
       echo $uploadedFile = $this->uploadFile();exit;


    }

    public function uploadFile()
 	{
		 // this folder will be created inside "pub/media" folder
		 $yourFolderName = 'import/csv/';
		 
		 // "upload_custom_file" is the HTML input file name
		 $yourInputFileName = 'filesubmission';
		 
		 try{
			 $file = $this->getRequest()->getFiles($yourInputFileName);
			 $fileName = ($file && array_key_exists('name', $file)) ? $file['name'] : null;
			 
			 if ($file && $fileName) {
					 $target = $this->mediaDirectory->getAbsolutePath($yourFolderName); 
					 
					 /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
					 $uploader = $this->fileUploader->create(['fileId' => $yourInputFileName]);
					 
					 // set allowed file extensions
					 $uploader->setAllowedExtensions(['csv']);
					 
					 // allow folder creation
					 $uploader->setAllowCreateFolders(true);
					 
					 // rename file name if already exists 
					 $uploader->setAllowRenameFiles(true); 
					 
					 // upload file in the specified folder
					 $result = $uploader->save($target);
					 
					 //echo '<pre>'; print_r($result); exit;
					 
					 if ($result['file']) {
					 	$filedata = [];
					 	$csvData = $this->csv->getData($target . $uploader->getUploadedFileName());
					 	     foreach ($csvData as $row => $data) {
						         if ($row > 0){
						             $filedata[] = $data;
						         }
						     }
						     $storeList = $this->storeRepository->getList();
						     $ids = [];
						     foreach ($storeList as $store) {
							    $ids[] = $store->getStoreId(); // store id
							}
							$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
							$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
							$connection = $resource->getConnection();
							$tableName = $resource->getTableName('catalog_product_entity'); 
							$tableName1 = $resource->getTableName('catalog_product_entity_decimal'); 
							$tableName2 = $resource->getTableName('catalog_product_entity_datetime'); 
							$entityid = [];
							$updateDatetime = new \DateTime();
								foreach ($filedata as $datar){
									$query = $connection->select()
						            ->from(
						                ['c' => $tableName],
						                ['entity_id']
						            )
						            ->where(
						                "c.sku = ?", $datar[0]
						            );
										$result = $connection->fetchAll($query);
										$priceFrom = $updateDatetime->modify($datar[3])->format('Y-m-d H:i:s');
			            				$priceTo = $updateDatetime->modify($datar[4])->format('Y-m-d H:i:s');
										$sql = "Update " . $tableName1. " Set value = ".$datar[2]." where store_id =0 and entity_id =".$result[0]['entity_id']." and attribute_id =78";
										$connection->query($sql);
										$store1 = "Update " . $tableName1. " Set value = ".$datar[2]." where store_id = 1 and entity_id =".$result[0]['entity_id']." and attribute_id =78";
										$connection->query($store1);
										$store2 = "Update " . $tableName1. " Set value = ".$datar[2]." where store_id = 2 and entity_id =".$result[0]['entity_id']." and attribute_id =78";
										$connection->query($store2);
									//	
									//	echo '<pre>';
									//	echo $connection->getSelect();
										$sql1 = "Update " . $tableName1. " Set value = ".$datar[1]." where store_id = 0 and entity_id =".$result[0]['entity_id']." and attribute_id =77";
										$connection->query($sql1);
										 $fromdate = "Update " . $tableName2. " Set value = ".$priceFrom." where store_id = 0 and entity_id =".$result[0]['entity_id']." and attribute_id =79";
										$connection->query($fromdate);
										$todate = "Update " . $tableName2. " Set value = ".$priceTo." where store_id = 0 and entity_id =".$result[0]['entity_id']." and attribute_id =80";
										$connection->query($todate);
										$date1 = "Update " . $tableName2. " Set value = ".$priceFrom." where store_id=1 and entity_id =".$result[0]['entity_id']." and attribute_id =79";
										$connection->query($date1);
										$enddate1 = "Update " . $tableName2. " Set value = ".$priceTo." where store_id=1 and entity_id =".$result[0]['entity_id']." and attribute_id =80";
										$connection->query($enddate1);
										
										$fromdatestore2 = "Update " . $tableName2. " Set value = ".$priceFrom." where store_id=2 and entity_id =".$result[0]['entity_id']." and attribute_id =79";
										$connection->query($fromdatestore2);
										
										$enddate = "Update " . $tableName2. " Set value = ".$priceTo." where store_id=2 and entity_id =".$result[0]['entity_id']." and attribute_id =80";
										$connection->query($enddate);
								}
					}
								//exit;

						
						     
					 	$this->messageManager->addSuccess(__('File has been successfully uploaded.')); 
					 }
					 
					 return $target . $uploader->getUploadedFileName();
		 		
		 	} catch (\Exception $e) {
				 $this->messageManager->addError($e->getMessage());
				 }
		 
		 return $target . $uploader->getUploadedFileName();
 	}
}

?>