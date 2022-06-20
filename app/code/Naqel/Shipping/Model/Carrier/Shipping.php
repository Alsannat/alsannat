<?php
namespace Naqel\Shipping\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'naqelshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('title')];
    }

    /**
     * @return float
     */
    private function getShippingPrice()
    {
        $configPrice = $this->getConfigData('price');
        $shippingPrice = $this->getFinalPriceWithHandlingFee($configPrice);
        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $total = $request->getPackageValue();
        $minTotal = $this->getConfigData('min_order_total');
        if(!empty($minTotal) && ($total < $minTotal)) {
            return false;
        }
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();
        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));
        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('title'));
        $amount = $this->getShippingPrice();
        $items = $request->getAllItems();
    		if($request->getDestCountryId() == 'BH'){

    			if (!empty($items)) {
    					$amount = 0;
    				foreach($items as $item){
    					$om         =   \Magento\Framework\App\ObjectManager::getInstance();
    					$pdata =   $om->create('\Magento\Catalog\Model\ProductRepository')->get($item->getSku());
    					$categories = $pdata->getCategoryIds(); /*will return category ids array*/
    					//print_r($categories);
    					$b = array(3, 4, 57, 68);
    					$c = array_intersect($categories, $b);
    					if (count($c) > 0) {
    						$amount = 35;
    						break;
    					}else{
    						$amount = 5;
    					}
    				}
    			}
    		}else{
    			if($total>300){
    				$amount = 0;
    			}
    		}
        $method->setPrice($amount);
        $method->setCost($amount);
        $result->append($method);
        return $result;
    }

     /**
     * Get  Billing Types provided for Home Delivery.
     *
     * @param string $type
     *
     * @return string
     */

     public function getBillingType($type)
     {
        $billingType = array(
            'A'   => 'On Account',
            'C'   => 'Cash',
            'COD' => 'Cash On Delivery'
        );
     }

}
