<?php $file = fopen('custom_status.csv', 'r', '"'); // set path to the CSV file
// put inside the magento root, run it by command line "php -f UpdateCustomStatus.php"

if ($file !== false) {

    //use Magento\Framework\App\Bootstrap;
    /**
     * If your external file is in root folder
     */
    require __DIR__ . './../app/bootstrap.php';

    /**
     * If your external file is NOT in root folder
     * Let's suppose, your file is inside a folder named 'xyz'
     *
     * And, let's suppose, your root directory path is
     * /var/www/html/magento2
     */
     //$rootDirectoryPath = '/public_html';
     //require $rootDirectoryPath . '/app/bootstrap.php';

    $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER);

    $objectManager = $bootstrap->getObjectManager();

    $state = $objectManager->get('Magento\Framework\App\State');
    $state->setAreaCode('adminhtml');

    // used for updating product info
    $productRepository = $objectManager->get('Magento\Catalog\Model\ProductRepository');

    // used for updating product stock
    $stockRegistry = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface');

    // add logging capability
    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import-update.log');
    $logger = new \Zend\Log\Logger();
    $logger->addWriter($writer);

    // enter the number of data fields you require the product row inside the CSV file to contain
    $required_data_fields = 2;  //change for 3 to 2

    $header = fgetcsv($file); // get data headers and skip 1st row
    $i = 0;
    while ( $row = fgetcsv($file, 350, ",") ) { //change to 350 at a time

        $data_count = count($row);
        if ($data_count < 1) {
            continue;
        }
        if($i < 1){
          $i++;
          continue;
        }
        if($i == 300){
          break;
        }
        $i++;

        $data = array();
        $data = array_combine($header, $row);

        $sku = $data['sku'];
        if ($data_count < $required_data_fields) {
            $logger->info("Skipping product sku " . $sku . ". Not enough data to import.");
            continue;
        }



        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////updating price/////////////////////////////////////////////////////////////////////
        try {
            $product = $productRepository->get($sku);
        }
        catch (\Exception $e) {
            $logger->info("Invalid product SKU: ".$sku);
            continue;
        }

        $status = trim($data['sku_n']); //use corresponding attribute id in your csv file
        //$price = trim($data['price']);
        echo 'Getting product SKU: '.$sku.', with Custom Status: '.$product->getSkuN().'<br />\r\n';
        echo 'Updating product SKU: '.$sku.', with Custom Status: '.$status.'<br />\r\n'; // .' and Price:'.$price.'<br />';
        $product->setSkuN($status)
        ->setStoreId(0) // this is needed because if you have multiple store views, each individual store view will get "Use default value" unchecked for multiple attributes - which causes issues.
        ->save();

    }
    fclose($file);
}
?>
