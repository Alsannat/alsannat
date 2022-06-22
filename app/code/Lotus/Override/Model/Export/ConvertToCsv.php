<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lotus\Override\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Amasty\CashOnDelivery\Model\PaymentFeeFactory;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;
use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;

/**
 * Class ConvertToCsv
 */
class ConvertToCsv extends \Magento\Ui\Model\Export\ConvertToCsv
{
    /**
     * @var WriteInterface
     */
    protected $directory;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var int|null
     */
    protected $pageSize = null;

    protected $orderRepository;

    /**
     * @var PaymentFeeFactory
     */
    private $paymentFeeFactory;

    /**
     * @var PaymentFeeResource
     */
    private $paymentFeeResource;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        PaymentFeeFactory $paymentFeeFactory,
        PaymentFeeResource $paymentFeeResource,
        $pageSize = 200
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->orderRepository = $orderRepository;
        $this->paymentFeeFactory = $paymentFeeFactory;
        $this->paymentFeeResource = $paymentFeeResource;
        $this->pageSize = $pageSize;
    }

    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     */
    public function getCsvFile()
    {
        $component = $this->filter->getComponent();
        
       // print_r($component->getName());die;

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.csv';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        if($component->getName() == 'sales_order_grid') {
            $header = array('Order id','customer', 'Mobile Number', 'Product sku', 'Product price', 'Qty', 'Product name','Product Weight','Total price', 'Shipping Address', 'Shipping Amount',
                'Signature', 'EMAIL', 'Payment Method', 'City', 'Region', 'Country', 'Order Status', 'Order Date', 'coupon code', 'discount amount', 'prd order id' , 'item id', 'tax','Shipping Company', 'COD Amount' , 'Tracking Number' , 'SMSA Status');
            $stream->writeCsv($header);
        }else {
            $stream->writeCsv($this->metadataProvider->getHeaders($component));
        }
        $i = 1;
        $productweight = 0;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int) $dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
                if($component->getName() == 'sales_order_grid') {
                    $order = $this->orderRepository->get($item->getEntityId());
                    $quoteId = $order->getQuoteId();

                    $paymentAmout = 0;
                    if($item->getPaymentMethod() == 'cashondelivery'){
                        $paymentFee = $this->paymentFeeFactory->create();
                        $this->paymentFeeResource->load($paymentFee, $quoteId, PaymentFeeInterface::QUOTE_ID);

                        if ($paymentFee->getEntityId()) {
                            $paymentAmout = $paymentFee->getAmount();
                        }
                    }

                    $billing = $order->getBillingAddress();
                    $region = ($order->getShippingAddress()) ? $order->getShippingAddress()->getRegion() : '';
                    $order_items = $order->getAllVisibleItems();
                    $shipment = $order->getShipmentsCollection()->getFirstItem();
                    $shipmentIncrementId = $shipment->getIncrementId();
                    $shippingCompany = '';
                    $trackingNumber = '';
                    $i = 0;
                    foreach ($shipment->getAllTracks() as $_item){
                        if($i < 1 ){
                            $shippingCompany.= $_item->getTitle();
                            $trackingNumber.= $_item->getNumber();
                        }else{
                            $shippingCompany.= ','.$_item->getTitle();
                            $trackingNumber.= ','.$_item->getNumber();
                        }
                        $i++;
                    }
                    
                    foreach ($order_items as $product) {
                        $productweight = ($product->getWeight() * $product->getQtyOrdered()) ; 
                        $content = array();
                        $content[] = $item->getIncrementId();
                        if(!empty($item->getCustomerName())) {
                            $content[] = $item->getCustomerName();
                        }else {
                            $content[] = $billing->getFirstname(). ' '. $billing->getLastname();
                        }
                        $content[] = $billing->getTelephone();
                        $content[] = $product->getSku();
                        $content[] = $product->getPrice();
                        $content[] = $product->getQtyOrdered();
                        $content[] = $product->getName();
                        $content[] = $productweight;
                        $content[] = $item->getGrandTotal();
                        $content[] = $item->getShippingAddress();
                        $content[] = $item->getShippingAndHandling();
                        $content[] = '';
                        $content[] = $item->getCustomerEmail();
                        $content[] = $item->getPaymentMethod();
                        $content[] = $billing->getCity();
                        $content[] = $region;
                        $content[] = $billing->getCountryId();
                        $content[] = $item->getStatus();
                        $content[] = date('m/d/Y h:i',strtotime($item->getCreatedAt()));
                        $content[] = $order->getCouponCode();
                        $content[] = abs($order->getDiscountAmount());
                        $content[] = $order->getEntityId();
                        $content[] = $product->getItemId();
                        $content[] = $order->getTaxAmount();
                        $content[] = $shippingCompany;
                        $content[] = $paymentAmout;
                        $content[] = $trackingNumber;
                        $content[] = $order->getData('awd_status');
                        $stream->writeCsv($content);
                    }
                }else {
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
