<?php
/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\ElasticsearchCore\Controller\Adminhtml\Browse;

/**
 * Class Raw
 */
class Raw extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Wyomind_ElasticsearchCore::browse';

    /**
     * @var \Magento\Framework\View\Result\PageFactory|null
     */
    public $resultPageFactory = null;

    /**
     * Raw constructor
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        return $this->getResponse()->representJson($this->getJsonData());
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getJsonData()
    {
        $indice = $this->getRequest()->getParam('indice');
        $storeCode = $this->getRequest()->getParam('store');
        $storeId = $this->getRequest()->getParam('storeId');
        $type = $this->getRequest()->getParam('type');
        $docId = $this->getRequest()->getParam('id');

        $config = new \Wyomind\ElasticsearchCore\Helper\Autocomplete\Config($storeCode);
        $config->getData();
        $client = new \Wyomind\ElasticsearchCore\Model\Client($config);
        $client->init($storeId);
        $data = $client->getByIds([$indice], $type, [$docId]);

        return json_encode($data['docs'][0], JSON_PRETTY_PRINT);
    }
}