<?php
namespace Lotus\Override\Ui\Component\Listing\Column;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Framework\View\Element\UiComponent\ContextInterface;
use \Magento\Framework\View\Element\UiComponentFactory;
use \Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use Amasty\CashOnDelivery\Model\PaymentFeeFactory;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;
use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;

class Shipment extends Column
{
    protected $_orderRepository;
    protected $_searchCriteria;

    /**
     * @var PaymentFeeFactory
     */
    private $paymentFeeFactory;

    /**
     * @var PaymentFeeResource
     */
    private $paymentFeeResource;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $criteria,
        PaymentFeeFactory $paymentFeeFactory,
        PaymentFeeResource $paymentFeeResource,
        array $components = [],
        array $data = []
    )
    {
        $this->_orderRepository = $orderRepository;
        $this->_searchCriteria  = $criteria;
        $this->paymentFeeFactory = $paymentFeeFactory;
        $this->paymentFeeResource = $paymentFeeResource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');
	
        if (isset($dataSource['data']['items'])) {
            
            foreach ($dataSource['data']['items'] as & $item) {
                $order  = $this->_orderRepository->get($item["entity_id"]);
                $track_number = $order->getData("tracking_number");
                $quoteId = $order->getQuoteId();
                $cod_amount = 0;
                $payment = $order->getPayment();
                $method = $payment->getMethodInstance();
                $paymentMethod = $order->getPayment()->getMethod();
                
                if($paymentMethod == 'cashondelivery'){
                    $paymentFee = $this->paymentFeeFactory->create();
                    $this->paymentFeeResource->load($paymentFee, $quoteId, PaymentFeeInterface::QUOTE_ID);

                    if ($paymentFee->getEntityId()) {
                        $cod_amount = $paymentFee->getAmount();
                    }
                }

                $shipment = $order->getShipmentsCollection()->getFirstItem();
                $shipmentIncrementId = $shipment->getIncrementId();
                $shippingCompany = '';
                $trackingNumber = '';
                $i = 0;
                $currency = $item['base_currency_code'];
                foreach ($shipment->getAllTracks() as $_item){
                    if($i < 1 ){
                        $shippingCompany.= $_item->getTitle();
                        $trackingNumber.= $_item->getNumber();
                    }else{
                        $shippingCompany.= ', '.$_item->getTitle();
                        $trackingNumber.= ', '.$_item->getNumber();
                    }
                    $i++;
                }
                
                $item['track_number'] = $trackingNumber;
                $item['title'] = $shippingCompany;
                $item['cod_amount'] = $priceHelper->currency($cod_amount, true, false);
            }
        }

        return $dataSource;
    }
}