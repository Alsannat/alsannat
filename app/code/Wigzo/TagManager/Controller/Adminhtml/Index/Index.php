<?php

namespace Wigzo\TagManager\Controller\Adminhtml\Index;


class Index extends \Magento\Backend\App\Action
{

    protected $resourceConfig;

    /*public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Config\Model\ResourceModel\Config $resourceConfig)
    {
        $this->resourceConfig = $resourceConfig;
        parent::__construct($context, $data);
    }*/

	public function saveAction()
    {
        $resp = array ();
        $params = $this->_request->getParams();
        unset ($params["via"]);

        $resourceConfig = $this->_objectManager->create ('\Magento\Config\Model\ResourceModel\Config');
        foreach ($params as $key => $value) {
			$resourceConfig->saveConfig ('admin/wigzo/'.$key, $value, 'default', 0);
        }

        $this->getResponse()->clearHeaders()->setHeader ('Content-type', 'application/json', true);
        $this->getResponse()->setBody ('{"status": "ok"}');
    }

    public function execute()
    {
        $via =  $this->getRequest()->getParam ('via');
        if ($via != null && $via == 'xmlhttp') {
            $this->saveAction();
            return;
        }
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
	}
}

