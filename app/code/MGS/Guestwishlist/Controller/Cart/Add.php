<?php

namespace MGS\Guestwishlist\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Add extends \Magento\Checkout\Controller\Cart
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        \MGS\Guestwishlist\Helper\Data $guesthelper,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager 

    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
        $this->guesthelper = $guesthelper;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cookieManager = $cookieManager;

    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get(
                \Magento\Store\Model\StoreManagerInterface::class
            )->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $params = $this->getRequest()->getParams();
        $productId = (int)$this->getRequest()->getParam('product');
        $itemid = $this->getRequest()->getParam('item');

        $product = $this->_initProduct();
        if($product->getTypeId()   == 'configurable' ){

            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl($product->getProductUrl());
            $this->messageManager->addSuccessMessage('you need to choose product option.');
            return $redirect;
          
        }
        elseif($product->getTypeId()   == 'simple'){
           
            $params['qty'] = 1;
        

            $product = $this->_initProduct();

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }
            try {
            $this->cart->addProduct($product, $params);

            $this->cart->save();
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setUrl('checkout/cart/index', ['_secure' => true]);
            $this->messageManager->addSuccessMessage('product add successfullay.');
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addException(
                        $e,
                        __('%1', $e->getMessage())
                    );
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('error.'));
                }

                $cookie = $this->guesthelper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME) != null 
                    ? $this->guesthelper->getCookie(\MGS\Guestwishlist\Helper\Data::COOKIE_NAME) : [];   

                    $cookie = $this->removeItemById($productId, $cookie);
                    $metadata = $this->_cookieMetadataFactory
                        ->createPublicCookieMetadata()
                        ->setPath('/')
                        ->setDuration(86400);
                    $this->_cookieManager->setPublicCookie(
                        \MGS\Guestwishlist\Helper\Data::COOKIE_NAME,
                        serialize($cookie),
                        $metadata
                    );
            
            return $this->goBack();
        }
      

        return $this->goBack();

    }

    /**
     * Resolve response
     *
     * @param string $backUrl
     * @param \Magento\Catalog\Model\Product $product
     * @return $this|\Magento\Framework\Controller\Result\Redirect
     */
    protected function goBack($backUrl = null, $product = null)
    {
        return parent::_goBack($this->_url->getUrl('guestwishlist'));
    }

    protected function removeItemById($itemId, $wishlist) {
        if ($wishlist !== null && is_array($wishlist)) {
            foreach ($wishlist as $productId => $items) {
                if($productId == $itemId){
                    unset($wishlist[$productId]);
                }
                // foreach ($items as $key => $_item) {
                //     if ($itemId == $productId) {
                //         unset($wishlist[$productId][$key]);
                //         // clean empty parent
                //         // unset parent if does not have any child products
                //         if (empty($wishlist[$productId])) {
                //             unset($wishlist[$productId]);
                //         }
                //     }
                // }
            }
        }
        return $wishlist;
    }
}

