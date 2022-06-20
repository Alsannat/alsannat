<?php

namespace Wyomind\ElasticsearchCore\Controller\Update;

/**
 * Update the customer group id when logged in
 */
class Cgi extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;


    /**
     * Cgi constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }


    /**
     * Execute action based on request and return result
     * @return \Magento\Framework\Controller\ResultInterface|\Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $result = ["cgi" => $this->_customerSession->getCustomer()->getGroupId()];
        } else {
            $result = ["cgi" => 0];
        }

        $this->getResponse()->representJson(json_encode($result));
    }
}