<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lotus\Override\Model\Export;
use Magento\Framework\Escaper;
use Amasty\CashOnDelivery\Api\Data\PaymentFeeInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Convert\Excel;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\SearchResultIteratorFactory;
use Amasty\CashOnDelivery\Model\PaymentFeeFactory;
use Amasty\CashOnDelivery\Model\ResourceModel\PaymentFee as PaymentFeeResource;

/**
 * Class ConvertToCsv
 */
class ConvertToXml extends \Magento\Ui\Model\Export\ConvertToXml
{   
    /**
     * @var DirectoryList
     */
    protected $directory;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var ExcelFactory
     */
    protected $excelFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var SearchResultIteratorFactory
     */
    protected $iteratorFactory;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Escaper
     */
    private $escaper;
    /**
     * Grid Header Array
     *
     * @var array
     */
    protected $_dataHeader = [];

    /**
     * Grid Footer Array
     *
     * @var array
     */
    protected $_dataFooter = [];

    protected $orderRepository;

    /**
     * @var PaymentFeeFactory
     */
    private $paymentFeeFactory;

    /**
     * @var PaymentFeeResource
     */
    private $paymentFeeResource;

    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ExcelFactory $excelFactory,
        SearchResultIteratorFactory $iteratorFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        PaymentFeeFactory $paymentFeeFactory,
        PaymentFeeResource $paymentFeeResource
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->excelFactory = $excelFactory;
        $this->iteratorFactory = $iteratorFactory;
        $this->orderRepository = $orderRepository;
        $this->paymentFeeFactory = $paymentFeeFactory;
        $this->paymentFeeResource = $paymentFeeResource;
    }
    public function getXmlFile()
    {
        $component = $this->filter->getComponent();
        
        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.xml';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        $component->getContext()->getDataProvider()->setLimit(0, 0);

        /** @var SearchResultInterface $searchResult */
        $searchResult = $component->getContext()->getDataProvider()->getSearchResult();

        /** @var DocumentInterface[] $searchResultItems */
        $searchResultItems = $searchResult->getItems();
        
        $this->prepareItems($component->getName(), $searchResultItems);

        /** @var SearchResultIterator $searchResultIterator */
        $searchResultIterator = $this->iteratorFactory->create(['items' => $searchResultItems]);
        
        /** @var Excel $excel */
        $excel = $this->excelFactory->create(
            [
                'iterator' => $searchResultIterator,
                'rowCallback'=> [$this, 'getRowData'],
            ]
        );

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        if($component->getName() == 'sales_order_grid') {

            $header = array('Order id','customer', 'Mobile Number', 'Product sku', 'Product price', 'Qty', 'Product name','Product Weight','Total price', 'Shipping Address', 'Shipping Amount',
                'Signature', 'EMAIL', 'Payment Method', 'City', 'Region', 'Country', 'Order Status', 'Order Date', 'coupon code', 'discount amount', 'prd order id' , 'item id', 'tax','Shipping Company', 'COD Amount' , 'Tracking Number','SMSA Status');

            $sheetName = 'sales_order_grid.xml';
            $contents = '<' .
            '?xml version="1.0"?' .
            '><' .
            '?mso-application progid="Excel.Sheet"?' .
            '><Workbook' .
            ' xmlns="urn:schemas-microsoft-com:office:spreadsheet"' .
            ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' .
            ' xmlns:x="urn:schemas-microsoft-com:office:excel"' .
            ' xmlns:x2="http://schemas.microsoft.com/office/excel/2003/xml"' .
            ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' .
            ' xmlns:o="urn:schemas-microsoft-com:office:office"' .
            ' xmlns:html="http://www.w3.org/TR/REC-html40"' .
            ' xmlns:c="urn:schemas-microsoft-com:office:component:spreadsheet">' .
            '<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">' .
            '</OfficeDocumentSettings>' .
            '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">' .
            '</ExcelWorkbook>' .
            '<Worksheet ss:Name="' .
            $sheetName .
            '"><Table>';
            
            $rowdata = $this->_writeRowdata($header);
            $contents .= $rowdata;

            foreach ($searchResultItems as $item) {
                $order = $this->orderRepository->get($item->getEntityId());
                //$quoteId = $order->getQuoteId();
                //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                //$order = $objectManager->create('Magento\Sales\Model\Order')->load($item->getEntityId());
                $order_items = $order->getAllVisibleItems();
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
                    $rowDataDetail = $this->_writeRowdata($content);
                    $contents .= $rowDataDetail;
                }
            }
            $contents .= '</Table></Worksheet></Workbook>';
            $stream->write($contents);
        }else {
            $excel->setDataHeader($this->metadataProvider->getHeaders($component));
            $excel->write($stream, $component->getName() . '.xml');
        }

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    public function _writeRowdata($data = array())
    {
        $xmlData = [];
        $xmlData[] = '<Row>';
        
        foreach ($data as $value) {
            $dataType = is_numeric($value) && $value[0] !== '+' && $value[0] !== '0' ? 'Number' : 'String';

            if (!is_string($value)) {
                $value = (string)$value;
            }
            if (isset($value[0]) && in_array($value[0], ['=', '+', '-'])) {
                $value = ' ' . $value;
                $dataType = 'String';
            }

            $value = str_replace("\r\n", '&#10;', $value);
            $value = str_replace("\r", '&#10;', $value);
            $value = str_replace("\n", '&#10;', $value);
            if($dataType == 'Number'){
                $value = trim($value);
            }
            $xmlData[] = '<Cell><Data ss:Type="' . $dataType . '">' . $value . '</Data></Cell>';
        }
        $xmlData[] = '</Row>';

        return join('', $xmlData);
    }

    protected function prepareItems($componentName, array $items = [])
    {
        foreach ($items as $document) {
            $this->metadataProvider->convertDate($document, $componentName);
        }
    }
}