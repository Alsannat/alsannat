<?php 
namespace Evincemage\ProductAttributes\Plugin;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ShipmentPlugin
{
	public function __construct(\Magento\Framework\Message\ManagerInterface $messageManager)
	{
		$this->messageManager = $messageManager;
	}
	public function aroundExecute(\Amasty\Oaction\Model\Command\Ship $subject, callable $proceed, $collection, $notifyCustomer, $oaction)
	{
		$orderToskip = [];
		//echo "before updating collection = ".count($collection);
		foreach ($collection as $key => $order)
		{
			$skipOrder = false;
			
			$orderIncId = $order->getIncrementId();
			//echo"id = ".$orderIncId;
			$OrderItems = $this->getOrderItems($orderIncId);
			if(count($OrderItems)>0)
			{

				foreach($OrderItems as $item)
				{
					//echo "item id = ".$item->getId()." product id = ".$item->getProductId();
					$categoryIdsNew = $this->getCategoryIdsFromProduct((int)$item->getProductId());//$this->getItemCategoryIds();
					//echo"<pre>";
					//print_r($categoryIdsNew);
					//echo"</pre>";
					if(in_array('52',$categoryIdsNew))
					{
						//echo "skip order id ".$orderIncId;
						$skipOrder = true;
						break;
					}
					$categoryIdsNew = [];
				}

				if($skipOrder)
				{
					$orderToskip[] = $orderIncId;
					$collection->removeItemByKey($key);
					continue;
				}
			}
		}
		if(!empty($orderToskip)&&(count($collection)>0))
		{
			$idsString = implode(",", $orderToskip);
			$this->messageManager->addError(__('%1 these order ids cannot be processed because it has multiple large skus, please do it manually', $idsString));
			$result = $proceed($collection, $notifyCustomer, $oaction);	
			return $result;
		}

		$result = $proceed($collection, $notifyCustomer, $oaction);
		return $result;
		
	}


	public function getOrderItems($incrementid)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId($incrementid);
		$orderItems = $order->getAllItems();
		return $orderItems;


	}

	/*public function getItemCategoryIds($item)
	{
		echo"called = ".$item;
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$product = $objectManager->get('Magento\Catalog\Model\Product')->load($item);
		echo "loaded product id = ".$product->getId();
		$itemCategories = $product->getCategoryIds();
		
		print_r($itemCategories);
		return $itemCategories;
	}*/

	public function getCategoryIdsFromProduct(int $productId)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$productCategoryList = $objectManager->create('\Magento\Catalog\Model\ProductCategoryList');
		$allCatIds = $productCategoryList->getCategoryIds($productId);
		$category = [];
        if ($allCatIds	) {
            $category = array_unique($allCatIds);
        }
        return $category;

	}
}