<?php
namespace WeltPixel\GoogleTagManager\Block;

/**
 * Class \WeltPixel\GoogleTagManager\Block\Order
 */
class Order extends \WeltPixel\GoogleTagManager\Block\Core
{

    protected $_image;

    protected $frontUrlModel;

    public function __construct(
        \Magento\Catalog\Helper\Image $_image,
        \Magento\Framework\UrlInterface $frontUrlModel
    ) {
        $this->_image = $_image;
        $this->frontUrlModel = $frontUrlModel;
    }

    private function getProductImageUrl($product) {
        return $this->_image->init($product, 'product_base_image')->constrainOnly(FALSE)
            ->keepAspectRatio(TRUE)
            ->keepFrame(FALSE)
            ->getUrl();
    }

    private function getProductUrl($product, $storeCode = 'default', $categoryId = null) {
        $routeParams = [ '_nosid' => true, '_query' => ['___store' => $storeCode]];

        $routeParams['id'] = $product->getId();
        $routeParams['s'] = $product->getUrlKey();
        
        if ($categoryId) {
            $routeParams['category'] = $categoryId;
        }
        
        return $this->frontUrlModel->getUrl('catalog/category/view', $routeParams);
    }

    /**
     * Returns the product details for the purchase gtm event
     * @return array
     */
    public function getProducts() {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog22.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('**************************');


        $order = $this->getOrder();
        $products = [];

        $displayOption = $this->helper->getParentOrChildIdUsage();

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if ($displayOption == \WeltPixel\GoogleTagManager\Model\Config\Source\ParentVsChild::CHILD) {
                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildrenItems();
                    foreach ($children as $child) {
                        $product = $child->getProduct();
                    }
                }
            }

            $productDetail = [];
            $productDetail['name'] = html_entity_decode($item->getName());
            $productDetail['id'] = $this->helper->getGtmProductId($product); //$this->helper->getGtmOrderItemId($item);
            $productDetail['price'] = number_format($item->getPrice(), 2, '.', '');
            if ($this->helper->isBrandEnabled()) :
                $productDetail['brand'] = $this->helper->getGtmBrand($product);
            endif;
            $categoryName = $this->helper->getGtmCategoryFromCategoryIds($product->getCategoryIds());
            $productDetail['category'] = $categoryName;
            $productDetail['list'] = $categoryName;
            $productDetail['quantity'] = $item->getQtyOrdered();
            $productDetail['product_url'] = $this->getProductUrl($product);
            $productDetail['image_url'] = $this->getProductImageUrl($product);
            $products[] = $productDetail;
        }
        $logger->info($products);

        return $products;
    }

    /**
     * Returns the product id's
     * @return array
     */
    public function getProductIds() {
        $order = $this->getOrder();
        $products = [];

        $displayOption = $this->helper->getParentOrChildIdUsage();

        foreach ($order->getAllVisibleItems() as $item) {
            $product = $item->getProduct();
            if ($displayOption == \WeltPixel\GoogleTagManager\Model\Config\Source\ParentVsChild::CHILD) {
                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $children = $item->getChildrenItems();
                    foreach ($children as $child) {
                        $product = $child->getProduct();
                    }
                }
            }

            $products[] = $this->helper->getGtmProductId($product); //$this->helper->getGtmOrderItemId($item);
        }

        return $products;
    }


    /**
     * Retuns the order total (subtotal or grandtotal)
     * @return float
     */
    public function getOrderTotal() {
        $orderTotalCalculationOption = $this->helper->getOrderTotalCalculation();
        $order =  $this->getOrder();
        switch ($orderTotalCalculationOption) {
            case \WeltPixel\GoogleTagManager\Model\Config\Source\OrderTotalCalculation::CALCULATE_SUBTOTAL :
                $orderTotal = $order->getSubtotal();
                break;
            case \WeltPixel\GoogleTagManager\Model\Config\Source\OrderTotalCalculation::CALCULATE_GRANDTOTAL :
            default:
                $orderTotal = $order->getGrandtotal();
                if ($this->excludeTaxFromTransaction()) {
                    $orderTotal -= $order->getTaxAmount();
                }
                if ($this->excludeShippingFromTransaction()) {
                    $orderTotal -= $order->getShippingAmount();
                }
            break;
        }

        return $orderTotal;
    }
}