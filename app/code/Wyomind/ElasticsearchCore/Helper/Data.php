<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Helper;

/**
 * Utilities
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|null
     */
    protected $_storeManager = null;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey = null;

    /**
     * @var \Magento\Framework\Module\Manager|null
     */
    protected $_moduleManager = null;


    protected $_customerSession = null;

    /**
     * Constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_storeManager = $storeManager;
        $this->_formKey = $formKey;
        $this->_moduleManager = $context->getModuleManager();
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get the elastic.php url for ajax requests
     * @param mixed $store
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getElasticsearchUrl($store = null)
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->_storeManager->getStore($store);
        $url = sprintf(
            '%selastic.php', $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB, $store->isCurrentlySecure())
        );

        return $url;
    }

    /**
     * Get the form key
     * @return string
     */
    public function getFormKey()
    {
        return $this->_formKey->getFormKey();
    }


    /**
     * In the admin ?
     * @return boolean
     */
    public function isAdmin()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $appState = $om->get('\Magento\Framework\App\State');
        $areaCode = $appState->getAreaCode();
        if ($areaCode == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Camelcapsize string
     * @param string $string
     * @return string
     */
    public function camelize($string)
    {
        return str_replace(" ", "", ucwords(str_replace("_", " ", $string)));
    }

    public function getCustomerGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    public function getNotificationMessage()
    {
        $message = __("Get the most of Elasticsearch!") . "<br/>";
        $message .= __("Upgrade to ");
        $tmpMessage = "";
        if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchMultifacetedAutocomplete')) {
            $tmpMessage .= "<a href='https://www.wyomind.com/magento2/elasticsearch-multifaceted-autocomplete-magento-2.html'>Elasticsearch Multifaceted Autocomplete</a>";
        }
        if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchLayeredNavigation')) {
            if (!$this->_moduleManager->isEnabled('Wyomind_ElasticsearchMultifacetedAutocomplete')) {
                $tmpMessage .= __(" and ");
            }
            $tmpMessage .= "<a href='https://www.wyomind.com/magento2/elasticsearch-layered-navigation-magento-2.html'>Elasticsearch Layered Navigation</a>";
        }
        if ($tmpMessage != "") {
            $tmpMessage .= ".";
            return $message . $tmpMessage;
        } else {
            return "";
        }
    }

}