<?php
 
namespace Ced\City\Controller;
 
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Ced\City\Helper\Data;
use Ced\City\Model\CityFactory;
 
abstract class City extends Action
{
   /**
    * @var \Magento\Framework\View\Result\PageFactory
    */
   protected $_pageFactory;
 
   /**
    * @var \Custom\City\Helper\Data
    */
   protected $_dataHelper;
 
   /**
    * @var \Custom\City\Model\CityFactory
    */
   protected $_cityFactory;
 
   /**
    * @param Context $context
    * @param PageFactory $pageFactory
    * @param Data $dataHelper
    * @param CityFactory $_cityFactory
    */
   public function __construct(
      Context $context,
      PageFactory $pageFactory,
      Data $dataHelper,
      CityFactory $cityFactory
   ) {
      parent::__construct($context);
      $this->_pageFactory = $pageFactory;
      $this->_dataHelper = $dataHelper;
      $this->_cityFactory = $cityFactory;
   }
 
}