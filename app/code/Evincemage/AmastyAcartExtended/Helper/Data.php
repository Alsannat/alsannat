<?php 
namespace Evincemage\AmastyAcartExtended\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getProductSkus()
    {
        return $this->scopeConfig->getValue(
            'emacart/general/product_skus',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}    