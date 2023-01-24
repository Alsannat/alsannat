<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Block\Adminhtml\System\Config\Form\Field;

class Link extends \Magento\Config\Block\System\Config\Form\Field
{
    protected $_template = 'Wyomind_GoogleProductRatings::system/config/link.phtml';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    public function __construct(\Wyomind\GoogleProductRatings\Helper\Delegate $wyomind, \Magento\Backend\Block\Template\Context $context, array $data = [])
    {
        $wyomind->constructor($this, $wyomind, __CLASS__);
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }
    /**
     * Return HTML element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }
    public function getLinkHtml()
    {
        $scopeIds['website'] = $this->_request->getParam('website');
        $scopeIds['store'] = $this->_request->getParam('store');
        // Get scope information
        $this->_scopeHelper->initScope($scopeIds);
        $storeManager = $this->_scopeHelper->getStoreManager();
        $directory = $this->_scopeHelper->getDirectory();
        $scope = $this->_scopeHelper->getScope();
        $scopeId = $this->_scopeHelper->getScopeId();
        $storeIds = $this->_scopeHelper->getStoreIds();
        $html = null;
        $fileUrl = null;
        $lastUpdate = null;
        $configFileName = $this->_scopeConfig->getValue(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_STORAGE_FILE_NAME, $scope, $scopeId);
        $configFilePath = $this->_scopeConfig->getValue(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_STORAGE_FILE_PATH, $scope, $scopeId);
        $fileName = $configFileName . '.xml';
        $filePath = $this->_storageHelper->getAbsoluteRootDir() . DIRECTORY_SEPARATOR . $configFilePath . $directory;
        if (false === $this->_storageHelper->fileExists($filePath, $fileName)) {
            $html .= '<span id="googleproductratings_alert" style="color:red;padding:2px;background:#FDFAB1;">' . __('No data feed generated. Please click the update button or define a schedule.') . '<br/></span>';
        } else {
            $baseUrl = $storeManager->getStore($storeIds[0])->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            $fileUrl = $baseUrl . $configFilePath . $directory . DIRECTORY_SEPARATOR . $fileName;
            $configUpdatedAt = $this->_scopeConfig->getValue(\Wyomind\GoogleProductRatings\Helper\Config::XML_PATH_STORAGE_UPDATED_AT, $scope, $scopeId);
            $lastUpdate = __('Last update: ') . $this->_coreDate->date('d M Y H:i:s', $configUpdatedAt);
        }
        $html .= '<a id="googleproductratings_link" href="' . $fileUrl . '">' . $fileUrl . '</a>' . '<br/><span id="googleproductratings_updated_at">' . $lastUpdate . '</span>';
        return $html;
    }
}