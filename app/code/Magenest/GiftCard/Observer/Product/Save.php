<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 31/08/2016
 * Time: 09:28
 */

namespace Magenest\GiftCard\Observer\Product;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
use \Magento\Framework\App\RequestInterface;
use \Magento\Store\Model\StoreManagerInterface;
use \Magenest\Subscription\Model\AttributeFactory;

class Save implements ObserverInterface
{
    protected $_logger;

    protected $_request;

    protected $_storeManager;

    protected $_backgroundFactory;

    protected $_artFactory;

    public function __construct(
        LoggerInterface $loggerInterface,
        RequestInterface $requestInterface,
        StoreManagerInterface $storeManagerInterface,
        \Magenest\GiftCard\Model\BackgroundFactory $backgroundFactoryFactory,
        \Magenest\GiftCard\Model\ArtFactory $artFactoryFactory
    ) {
        $this->_logger = $loggerInterface;
        $this->_request = $requestInterface;
        $this->_storeManager = $storeManagerInterface;

        $this->_artFactory = $artFactoryFactory;

        $this->_backgroundFactory = $backgroundFactoryFactory;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $this->_request->getParams();
        print_r($data);exit;
        $product = $observer->getProduct();
        $productId = $product->getId();

        /////////////Background
        $collection = $this->_backgroundFactory->create()->getCollection() ->addFieldToFilter('product_id', $productId);

        if ($collection->getSize()) {
            foreach ($collection as $background) {
                $background->delete();
            }
        }


        ///////////////////////
        if (isset($data['product']['giftcard']['link'])) {
            $backgrounds = $data['product']['giftcard']['link'];

            if ($backgrounds) {
                foreach ($backgrounds as $background) {

                    if (isset($background['file'])
                     && isset($background['title'])
                      && isset($background['record_id'])
                    ) {
                        $del = false;
                        if (isset($background['is_delete']) && $background['is_delete'] == "1") $del = true;
                    $status='';
                    $title = $background['title'];
                    $record_id = $background['record_id'];
                    $file = $background['file'];

                    if (is_array($file) && !$del) {
                        $name = (isset($file[0]['name']))?$file[0]['name']:'';
                        $path = (isset($file[0]['file']))? $file[0]['file']:'';
                        $size = (isset($file[0]['size']))? $file[0]['size']:'';
                        $status = (isset($file[0]['status']))? $file[0]['status'] :'';

                        if ($path) {
                            $model = $this->_backgroundFactory->create();
                            $model->setData([
                                'title' => $title,
                                'product_id' => $productId,
                                'file' => $path,
                                'name' =>$name,
                                'size' =>$size
                            ])->save();
                        }

                    }

                    //if the background is old one then load it and modify it
                }//end foreach
            }
        }

        //delete the old art and save the new one
        $artCollection = $this->_artFactory->create()->getCollection();

        if ($artCollection->getSize()) {
            foreach ($artCollection as $art) {
                $art->delete();
            }
        }

        ///////////////////////
        if (isset($data['product']['giftcard']['art'])) {
            $arts = $data['product']['giftcard']['art'];

            if ($arts) {
                foreach ($arts as $art) {
                    if (isset($art['title']) && isset($art['file'])) {
                        $is_del = false;

                        if (isset($art['is_delete']) && $art['is_delete'] == '1') $is_del = true;
                        $status = '';
                        $title = $art['title'];
                        $file = $art['file'];

                        if (is_array($file) && !$is_del) {
                            $name = (isset($file[0]['name'])) ? $file[0]['name'] : '';
                            $path = (isset($file[0]['file'])) ? $file[0]['file'] : '';
                            $size = (isset($file[0]['size'])) ? $file[0]['size'] : '';
                            $status = (isset($file[0]['status'])) ? $file[0]['status'] : '';

                            //if the background is old one then load it and modify it

                            $model = $this->_artFactory->create();

                            $model->setData([
                                'title' => $title,
                                'product_id' => $productId,
                                'file' => $path,
                                'name' => $name,
                                'size' => $size
                            ])->save();
                        }

                    }
                }
                } //end foreach
            }
        }
    }
}
