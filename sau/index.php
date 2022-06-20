<?php
require realpath(__DIR__) . '/../app/bootstrap.php';
$params = $_SERVER;
$params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'sau'; 
$params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = 'website'; 
$bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);

$app = $bootstrap->createApplication('Magento\Framework\App\Http');
$bootstrap->run($app);
?>