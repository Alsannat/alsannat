<?php

namespace Webengage\Event\Controller\Index;
use Magento\Backend\App\Action;

class Refreshinfo extends \Magento\Framework\App\Action\Action
{

    protected $_pageFactory;
    protected $backendSession;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\Model\Session $backendSession)
    {
        $this->_pageFactory = $pageFactory;
        $this->backendSession = $backendSession;

        return parent::__construct($context);
    }

    public function execute()
    {
        if (isset($_POST) && !empty($_POST) && isset($_POST['weLuid']) && $_POST['weLuid'] != '') {
            $this->backendSession->setLuid($_POST['weLuid']);
            $this->backendSession->setSuid($_POST['suid']);

        }
        else {
            $this->backendSession->setLuid($this->backendSession->getLuid());
            $this->backendSession->setSuid($this->backendSession->getSuid());
        }


    }
}
