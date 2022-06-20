<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Bulkshipment;

use Naqel\Shipping\Model\WaybillFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
ob_start();

class Index extends \Magento\Framework\App\Action\Action
{
  protected $_filesystem;
  protected $_pageFactory;
  protected $_WaybillFactory;
  protected $request;
  protected $helperData;
  protected $messageManager;
  protected $responseFactory;
  protected $redirect;
  protected $_objectManager;

  /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory
     * @param \Naqel\Shipping\Helper\Data $helperData
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\ObjectManagerInterface $objectmanager
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param array $data
     */
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Message\ManagerInterface $messageManager,
    \Magento\Framework\View\Result\PageFactory $pageFactory,
    \Magento\Framework\App\Request\Http $request,
    \Naqel\Shipping\Model\WaybillFactory  $WaybillFactory,
    \Naqel\Shipping\Helper\Data $helperData,
    \Magento\Framework\Filesystem $filesystem,
    \Magento\Framework\App\Response\RedirectInterface $redirect,
    \Magento\Framework\ObjectManagerInterface $objectmanager,
    \Magento\Framework\App\ResponseFactory $responseFactory
    )
  {
    $this->_pageFactory = $pageFactory;
    $this->messageManager = $messageManager;
    $this->request = $request;
    $this->helperData = $helperData;
    $this->_WaybillFactory = $WaybillFactory;
    $this->_filesystem = $filesystem;
    $this->redirect = $redirect;  
    $this->responseFactory = $responseFactory;
    $this->_objectManager = $objectmanager;
    parent::__construct($context);
  }


  public function execute()
  {
	  	//echo "<pre>";
	  	$data = $this->request->getParams();
	  	$orders_array = $data['selected'];
	  	if(isset($orders_array))
	  	{

	  		foreach ($orders_array as $key => $value) 
	  		{
	  			//load by order 
				$order = $this->_objectManager->create('Magento\Sales\Model\Order')
				    ->load($value);

				// Check if order can be shipped or has already shipped
				if (! $order->canShip()) 
				{
					$message = 'You can\'t create an shipment for  order - '. $value;	
					$this->returnBackWithError($message);
	                die();
				}

				// Initialize the order shipment object
				$convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
				$shipment = $convertOrder->toShipment($order);

				// Loop through order items
				foreach ($order->getAllItems() AS $orderItem) 
				{
				    // Check if order item has qty to ship or is virtual
				    if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
				        continue;
				    }

				    $qtyShipped = $orderItem->getQtyToShip();

				    // Create shipment item with qty
				    $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

				    // Add shipment item to shipment
				    $shipment->addItem($shipmentItem);
				}

				// Register shipment
				$shipment->register();

				$shipment->getOrder()->setIsInProcess(true);

				try 
				{
				    // Save created shipment and order
				    $shipment->save();
				    $shipment->getOrder()->save();

				    // Send email
				    $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
				        ->notify($shipment);

				    $shipment->save();
				} catch (\Exception $e) {
					$message = $e->getMessage();	
					$this->returnBackWithError($message);
	                
				}
	  		}

	  	}
    }

	/**
	 * Return back with error message
	 *
     * @param string
     * @return null
     */    
    public function returnBackWithError($message)
	  {
	          $this->helperData->naqelLogger($message); 
	          $this->messageManager->addError($message);
	          $redirectUrl = $this->redirect->getRedirectUrl();
	          $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
	          die();
	  }
}