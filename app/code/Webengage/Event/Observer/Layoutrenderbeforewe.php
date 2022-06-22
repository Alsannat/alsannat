<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;
use Magento\Framework\Json\EncoderInterface;

class Layoutrenderbeforewe implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var EncoderInterface
     */
    private $_jsonEncoder;

    /**
     * Layoutrenderbeforewe constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Helper\Image $image
     * @param Data $helper
     */
    public function __construct(\Magento\Framework\App\RequestInterface $request,
                                \Magento\Store\Model\StoreManagerInterface $storeManager,
                                \Magento\Checkout\Model\Cart $cart,
                                \Magento\Catalog\Helper\Image $image,
                                \Magento\Catalog\Model\Product $product,
                                \Magento\Framework\Registry $registry,
                                EncoderInterface $encoder,
                                Data $helper)
    {
        $this->helper = $helper;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->cart = $cart;
        $this->image = $image;
        $this->product = $product;
        $this->registry = $registry;
        $this->_jsonEncoder = $encoder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventCheck = array('checkout_cart_index', 'checkout_index_index', 'catalog_category_view','catalogsearch_result_index');

        $requestInterface = $this->request;
        $getCurrentActionName = trim(strtolower($requestInterface->getFullActionName()));

        $licenceCode = $this->helper->getLicenceInfo();

        if ($licenceCode != '') {

            if (in_array($getCurrentActionName, $eventCheck)) {

                $prepareJson = array();

                /*Checkout & Cart Page Event*/
                if ($getCurrentActionName == 'checkout_cart_index' || $getCurrentActionName == 'checkout_index_index') {

                    $cartObj = $this->cart;
                    $totalItems = $cartObj->getQuote()->getItemsCount();
                    $productinformation = $this->getAllProductFromCart();
                    $cartData = (object)$cartObj->getQuote()->getData();
                    $eventName = '';
                    if ($getCurrentActionName == 'checkout_cart_index') {
                        $eventName = 'Cart Viewed';
                    } else if ($getCurrentActionName == 'checkout_index_index') {
                        $eventName = 'Checkout Started';
                    }

                    $couponcode = '';
                    if (isset($cartData->coupon_code) && trim($cartData->coupon_code) != '') {
                        $couponcode = $cartData->coupon_code;
                    }
                    $subtotalWithDiscount = '';
                    if (isset($cartData->subtotal_with_discount) && trim($cartData->subtotal_with_discount) != '') {
                        $subtotalWithDiscount = $cartData->subtotal_with_discount;
                    }

                    // Product fields
                    $productIds = array();
                    $categoryIds = array();
                    $categoryNames = array();

                    if ( !empty($productinformation) ) {

                        foreach ($productinformation as $p) {
                            array_push($productIds, $p['productId']);
                            array_push($categoryIds, implode(",", $p['categoryId']));
                            array_push($categoryNames, $p['categoryName']);
                        }

                        $prepareJson = array(
                                'event_name' => $eventName,
                                'event_data' => array(
                                    'totalItemInCart' => (float)$totalItems,
                                    'productInCart' => $productinformation,
                                    'cartGrandTotal' => (float)$cartObj->getQuote()->getGrandTotal(),
                                    'couponCodeUsed' => $couponcode,
                                    'subtotalWithDiscount' => (float)$subtotalWithDiscount,
                                    'productIds' => implode(",", $productIds),
                                    'categoryIds' => implode(",", $categoryIds),
                                    'categoryNames' => implode(",", $categoryNames),
                                )
                        );
                    }
                }
                /*Catalog Category view page*/
                elseif ($getCurrentActionName == 'catalog_category_view') {
                    $catData = $this->registry->registry('current_category');
                    $allcategoryproduct = $catData->load($catData->getId())->getProductCollection()->addAttributeToSelect('*');
                    $count = (float)$allcategoryproduct->count();


                    $prepareJson = array(
                            'event_name' => 'Category Viewed',
                            'event_data' => array(
                                'categoryName' => $catData->getName(),
                                'categoryDesc' => $catData->getDescription(),
                                'categoryUrl' => $catData->getUrl(),
                                'totalProductsInCategory' => $count,
                        )
                    );
                }
                
                /*Catalog Search Result page*/
                elseif ($getCurrentActionName == 'catalogsearch_result_index') {
                    if ( isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') {

                        $prepareJson = array(
                            'event_name' => 'Searched',
                            'event_data' => array(
                                'searchKeyword' => $_REQUEST['q'],
                                'searchResult' => null,
                                'totalProducts' => null,
                            )
                        );
                    }
                }
                

                if (!empty($prepareJson)) {
                    /*Calling WE API*/
                    $this->helper->apiCallToWebengage($prepareJson);
                    /*Calling WE API*/

                     return $this;
                }
            }
        }
    }

    function getAllProductFromCart()
    {
        $cartObj = $this->cart;
        $totalItemsInCart = $cartObj->getQuote()->getItemsCount();
        $storeManager = $this->storeManager;
        $storeName = $storeManager->getStore()->getName();
        $storeCode = $storeManager->getStore()->getCode();
        $productinformation = array();
        if ($totalItemsInCart > 0) {
            $items = $cartObj->getQuote()->getAllItems();
            foreach ($items as $item) {
                $product = $this->product->load($item->getProductId());
                $imageHelper = $this->image;
                $productImageUrl = $imageHelper->init($product, 'product_page_image_large')->getUrl();
                $OrgPrice = (float)$product->getPrice();
                $productinformation[] = array(
                    'productId' => $item->getProductId(),
                    'productName' => $product->getName(),
                    'productDescription' => $product->getDescription(),
                    'productShortDescription' => $product->getShortDescription(),
                    'productSku' => $product->getSku(),
                    'productUrl' => $product->getProductUrl(),
                    'productImage' => $productImageUrl,
                    'productOriginalPrice' => $OrgPrice,
                    'productQty' => (float)$item->getQty(),
                    'storeName' => $storeName,
                    'storeCode' => $storeCode,
                    'categoryName' => $this->helper->getProductCategories($product),
                    'categoryId' => $product->getCategoryIds()
                );
            }

            return $productinformation;
        }
    }


}
