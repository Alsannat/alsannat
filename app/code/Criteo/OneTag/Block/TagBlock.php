<?php

namespace Criteo\OneTag\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order;
use Criteo\OneTag\Helper\TagGenerator;
use Magento\Framework\ObjectManagerInterface;

/**
 * Description of TagBlock
 *
 * @author Criteo
 */
class TagBlock extends \Magento\Framework\View\Element\Template
{
    const SETTINGS_PARTNER_ID = 'cto_onetag_section/general/cto_partner';
    const SETTINGS_ENABLE_HOME = 'cto_onetag_section/general/cto_enable_home';
    const SETTINGS_ENABLE_LISTING = 'cto_onetag_section/general/cto_enable_listing';
    const SETTINGS_ENABLE_PRODUCT = 'cto_onetag_section/general/cto_enable_product';
    const SETTINGS_ENABLE_BASKET = 'cto_onetag_section/general/cto_enable_basket';
    const SETTINGS_ENABLE_SALE = 'cto_onetag_section/general/cto_enable_sale';
    const SETTINGS_USE_SKU = 'cto_onetag_section/general/cto_use_sku';

    protected $_request;
    protected $_registry;
    protected $_scopeConfig;
    protected $_customerSession;
    protected $_cart;
    protected $_order;
    protected $_tagGenerator;
    protected $_useSku;
    protected $_objectManager;
    protected $_storeManager;
	protected $_productloader;

    public function __construct(
        Context $context,
        Http $request,
        Registry $registry,
        \Magento\Customer\Model\Session $customer_session,
        \Magento\Checkout\Model\Session $cart,
        Order $order,
        TagGenerator $tag_generator,
        ObjectManagerInterface $objectManager,
		\Magento\Catalog\Model\ProductFactory $productloader,
        array $data = []
    ) {
        $this->_request = $request;
        $this->_registry = $registry;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_customerSession = $customer_session;
        $this->_cart = $cart;
        $this->_order = $order;
        $this->_tagGenerator = $tag_generator;
        $this->_useSku = $this->_scopeConfig->getValue(self::SETTINGS_USE_SKU, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_objectManager = $objectManager;
        $this->_storeManager = $context->getStoreManager();
		$this->_productloader = $productloader;

        parent::__construct($context, $data);
    }

    public function cto_generate_tag()
    {
        $output = '';

        //get configuration info
        $partnerId = $this->_scopeConfig->getValue(self::SETTINGS_PARTNER_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableHome = $this->_scopeConfig->getValue(self::SETTINGS_ENABLE_HOME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableListing = $this->_scopeConfig->getValue(self::SETTINGS_ENABLE_LISTING, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableProduct = $this->_scopeConfig->getValue(self::SETTINGS_ENABLE_PRODUCT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableBasket = $this->_scopeConfig->getValue(self::SETTINGS_ENABLE_BASKET, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $enableSale = $this->_scopeConfig->getValue(self::SETTINGS_ENABLE_SALE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        //get user email if available
        $email = '';
        if ($this->_customerSession->isLoggedIn()) {
            $email = $this->_customerSession->getCustomer()->getEmail();
        }

        if (!empty($email)) {
            $email = md5(strtolower(trim($email)));
        }

        //set partner id
        $this->_tagGenerator->setAccount(array('account' => $partnerId));

        //set user email
        $this->_tagGenerator->setEmail(array('email' => $email));

        if ($this->is_homepage()) {
            $this->_tagGenerator->viewHome();

            if ($enableHome) {
                $output = $this->_tagGenerator->cto_get_code();
            }
        } elseif ($this->is_listing()) {
            $product_list = $this->get_product_list();
            $this->_tagGenerator->viewList($product_list);

            if ($enableListing) {
                $output = $this->_tagGenerator->cto_get_code();
            }
        } elseif ($this->is_product()) {
            $product_id = $this->get_product_id();
            $this->_tagGenerator->viewItem($product_id);

            if ($enableProduct) {
                $output = $this->_tagGenerator->cto_get_code();
            }
        } elseif ($this->is_cart()) {
            $items = $this->get_cart();
            $this->_tagGenerator->viewBasket($items);

            if ($enableBasket) {
                $output = $this->_tagGenerator->cto_get_code();
            }
        } elseif ($this->is_sale()) {
            $this->_tagGenerator->trackTransaction($this->get_sale_data());

            if ($enableSale) {
                $output = $this->_tagGenerator->cto_get_code();
            }
        }

        //show data layer regardless of enable options
        $output .= $this->_tagGenerator->cto_set_dataLayer();

        return $output;
    }

    private function is_homepage()
    {
        return $this->_request->getFullActionName() == 'cms_index_index';
    }

    private function is_listing()
    {
        return $this->_request->getFullActionName() == 'catalog_category_view';
    }

    private function is_product()
    {
        return $this->_request->getFullActionName() == 'catalog_product_view';
    }

    private function is_cart()
    {
        $action_name = $this->_request->getFullActionName();
        return $action_name =='checkout_cart_index' || $action_name == 'checkout_index_index'  || $action_name == 'checkout_onepage_index' || $action_name == 'firecheckout_index_index';
    }

    private function is_sale()
    {
		 $action_name = $this->_request->getFullActionName();
		 return $action_name == "checkout_onepage_success" || $action_name == "onepagecheckout_index_success";
    }

    private function get_product_id()
    {
        $product_id = '';

        try {
            $current_product = $this->_registry->registry('current_product');
            $product_id = $this->_useSku ?  $current_product->getSku() : $current_product->getId();
        } catch (\Exception $exc) {
            //do nothing
        }
        return array('item' => $product_id);
    }

    private function get_product_list()
    {
        $product_list = array();
        try {
            $products = $this->_registry->registry('current_category')->getProductCollection();
            foreach ($products as $product) {
                $product_list[] = $this->_useSku ? $product->getSku() : $product->getId();
                if (sizeof($product_list) == 3) {
                    break;
                }
            }
        } catch (\Exception $exc) {
            //do nothing
        }

        return array('item' => $product_list);
    }

    private function get_cart()
    {
        $cart_content = array();
        $currency_code = '';
        try {
            $products = $this->_cart->getQuote()->getAllVisibleItems();
            foreach ($products as $product) {
				$price = (float) $product->getPrice();
				$quantity = (int) $product->getQty();
				$product = $this->get_parent_product_if_any($product);
                $product_id = $this->_useSku ?  $product->getSku() : $product->getId();
                $cart_content[] = array(
                    'id' => $product_id,
                    'price' => $price,
                    'quantity' => $quantity
                );
            }

            $currency_code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        } catch (\Exception $exc) {
            // do nothing
        }

        return array('item' => $cart_content);
    }

    private function get_sale_data()
    {
        $transaction_id = '';
        $purchased_products = array();
        $currency_code = '';

        try {
            $order_id = $this->_cart->getLastOrderId();
            $order = $this->_order->load($order_id);

            //get transaction id (known to customer)
            $transaction_id = $order->getIncrementId();

            //get purchased products
            $products = $order->getAllVisibleItems();
            foreach ($products as $product) {
			    $price = (float) $product->getPrice();
				$quantity = (int) $product->getQtyOrdered();
				$product = $this->get_parent_product_if_any($product);
                $product_id = $this->_useSku ? $product->getSku() : $product->getId();
                $purchased_products[] = array(
                    'id' => $product_id,
                    'price' => $price,
                    'quantity' => $quantity
                );
            }

            $currency_code = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        } catch (\Exception $exc) {
            //do nothing
        }

        return array('id' => $transaction_id, 'currency' => $currency_code, 'item' => $purchased_products);
    }
	
	private function get_parent_product_if_any($product) {
		if (!$product) {
            return;
        }

        //use product parent ID if any
        $product_id = $product->getProductId();
		return $this->_productloader->create()->load($product_id);
		
	}
}
