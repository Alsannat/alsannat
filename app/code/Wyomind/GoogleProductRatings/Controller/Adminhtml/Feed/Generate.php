<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Wyomind\GoogleProductRatings\Controller\Adminhtml\Feed;

class Generate extends \Magento\Backend\App\Action
{
    /**
     * @var \Wyomind\GoogleProductRatings\Helper\Feed
     */
    public $feedHelper = null;
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory = null;
    
    /**
     * @var string
     */
    protected $_aclResource = 'generation';

    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * Class constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Wyomind\GoogleProductRatings\Helper\Feed $feedHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Wyomind\GoogleProductRatings\Helper\Feed $feedHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    
        $this->feedHelper = $feedHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $scope['website'] = $this->_request->getParam('website');
        $scope['store'] = $this->_request->getParam('store');

        /*if ($scope['website'] == null || $scope['store'] == null) {
            $scope['website'] = $this->storeManager->getDefaultStoreView()->getWebsiteId();
            $scope['store'] = $this->storeManager->getDefaultStoreView()->getId();
        }*/

        try {
            $result = $this->feedHelper->generate($scope);
        } catch (\Exception $e) {
            $result = ['link' => $e->getMessage()];
        }
        
        $resultJson = $this->resultJsonFactory->create();
        
        return $resultJson->setData($result);
    }
    
    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Wyomind_GoogleProductRatings::' . $this->_aclResource);
    }
}
