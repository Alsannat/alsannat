<?php
/**
 * Created by PhpStorm.
 * User: thuy
 * Date: 12/05/2017
 * Time: 16:32
 */

namespace Magenest\GiftCard\Helper;

use Magento\Catalog\Api\ProductRepositoryInterface;

class Cart extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    protected $cart;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Checkout\Model\Cart $cart,
        ProductRepositoryInterface $productRepository
    ) {

        $this->productRepository = $productRepository;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->cart = $cart;
        parent::__construct($context);
    }

    public function addToCart($params)
    {
        $productId = $params['productId'];
        $product = $this->_initProduct($productId);

        $this->cart->addProduct($product, $params);
        $this->cart->save();
    }
    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($productId);
                return $product;
            } catch (\Exception $exception) {
                return false;
            }
        }
        return false;
    }

    /**
     * @return mixed|void
     */
    public function getBuyRequest()
    {

        $request = parent::_getRequest();
        $quoteItemId = $request->getParam('id');

        if (!$quoteItemId) return;

        $productId = $request->getParam('product_id');
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $objectManager->create('Magento\Quote\Model\Quote\Item')->load($quoteItemId);
        if (!is_object($quoteItem)) return;


        /** get the q */
        $collection = $objectManager->create('Magento\Quote\Model\Quote\Item\Option')->getCollection()
            ->addFieldToFilter('item_id',$quoteItemId )
            ->addFieldToFilter('code','info_buyRequest' );

        $buyRequest = $collection->getFirstItem();
        $buyRequestParams =unserialize($buyRequest->getValue());
        return $buyRequestParams;
    }

}
