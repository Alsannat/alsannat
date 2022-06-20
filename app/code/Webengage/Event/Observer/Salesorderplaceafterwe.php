<?php

namespace Webengage\Event\Observer;

use Webengage\Event\Helper\Data;


class Salesorderplaceafterwe implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var \Magento\Catalog\Model\Product
     */
    private $product;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $image;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Salesorderplaceafterwe constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Image $image
     * @param Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Customer\Model\Session $customerSession,
        Data $helper)
    {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->resourceConnection = $resourceConnection;
        $this->request = $request;
        $this->product = $product;
        $this->image = $image;
        $this->customerSession = $customerSession;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $storeManager = $this->storeManager;
        $storeName = $storeManager->getStore()->getName();
        $storeCode = $storeManager->getStore()->getCode();

        $shippingAddress = array();
        if (!empty($order->getShippingAddress())) {
            $shippingAddress = (object)$order->getShippingAddress()->getData();
        }


        $billingAddress = (object)$order->getBillingAddress()->getData();

        if (empty($shippingAddress)) {
            $shippingAddress = $billingAddress;
        }
        $customerEmail = $billingAddress->email;
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            $customerEmail = $customerSession->getCustomer()->getEmail();
        }


        $incrementId = $order->getIncrementId();
        $couponCode = "";
        if ($order->getCouponCode() && $order->getCouponCode() != '') {
            $couponCode = $order->getCouponCode();
        }

        $productinformation = array();
        foreach ($order->getAllItems() as $item) {
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
                'productQty' => (float)$item->getQtyOrdered(),
                'Ordered Qty' => (float)$item->getQtyOrdered(),
                'categoryName' => $this->helper->getProductCategories($product),
                'categoryId' => $product->getCategoryIds()
            );
        }

        $customerInformation = array(
            'customerFirstName' => $shippingAddress->firstname,
            'customerMiddleName' => trim($shippingAddress->middlename) != '' ? $shippingAddress->middlename : null,
            'customerLastName' => $shippingAddress->lastname,
            'customerEmail' => $shippingAddress->email,
        );

        $billingRegionName = $billingAddress->region;
        $shippingRegionName = $shippingAddress->region;


        $prepareShippingAddress = array(
            'customerFirstName' => $shippingAddress->firstname,
            'customerMiddleName' => trim($shippingAddress->middlename) != '' ? $shippingAddress->middlename : null,
            'customerLastName' => $shippingAddress->lastname,
            'customerStreet' => $shippingAddress->street,
            'customerTelephone' => $shippingAddress->telephone,
            'customerPostCode' => $shippingAddress->postcode,
            'customerEmail' => $customerEmail,
            'customerRegionName' => $shippingRegionName,
            'customerRegion' => $shippingAddress->region,
            'customerCountry' => $shippingAddress->country_id,
            'storeName' => $storeName,
            'storeCode' => $storeCode,
        );

        $prepareBillingAddress = array(
            'customerFirstName' => $billingAddress->firstname,
            'customerMiddleName' => trim($billingAddress->middlename) != '' ? $billingAddress->middlename : null,
            'customerLastName' => $billingAddress->lastname,
            'customerStreet' => $billingAddress->street,
            'customerTelephone' => $billingAddress->telephone,
            'customerPostCode' => $billingAddress->postcode,
            'customerEmail' => $customerEmail,
            'customerRegionName' => $billingRegionName,
            'customerRegion' => $billingAddress->region,
            'customerCountry' => $billingAddress->country_id,
            'storeName' => $storeName,
            'storeCode' => $storeCode,
        );

        // Product fields
        $productIds = array();
        $categoryIds = array();
        $categoryNames = array();

        foreach ($productinformation as $p) {
            array_push($productIds, $p['productId']);
            array_push($categoryIds, implode(",", $p['categoryId']));
            array_push($categoryNames, $p['categoryName']);
        }

        $prepareJson = array(
                'cuid' => $customerEmail,
                'event_name' => 'Checkout Completed',
                'event_data' => array(
                    'orderID' => $incrementId,
                    'orderGrandTotal' => (float)$order->getGrandTotal(),
                    'orderSubTotal' => (float)$order->getSubtotal(),
                    'orderShippingAmount' => (float)$order->getShippingAmount(),
                    'orderDiscountAmount' => (float)$order->getDiscountAmount(),
                    'orderTaxAmount' => (float)$order->getTaxAmount(),
                    'couponCodeUsed' => $couponCode != '' ? $couponCode : null,
                    'orderProductInformation' => $productinformation,
                    'customerInformation' => $customerInformation,
                    'customerBillingInformation' => $prepareBillingAddress,
                    'customerShippingInformation' => $prepareShippingAddress,
                    'storeName' => $storeName,
                    'storeCode' => $storeCode,
                    'productIds' => implode(",", $productIds),
                    'categoryIds' => implode(",", $categoryIds),
                    'categoryNames' => implode(",", $categoryNames),
                )
        );

        /*Calling WE API*/
        $this->helper->apiCallToWebengage($prepareJson);
        /*Calling WE API*/


    }

}
