<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vnecoms\PdfPro\Model\Key;

use Vnecoms\PdfPro\Model\ResourceModel\Key\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider.
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;
    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Vnecoms\PdfPro\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * DataProvider constructor.
     *
     * @param \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory $collectionFactory
     * @param string                                                         $name
     * @param string                                                         $primaryFieldName
     * @param string                                                          $requestFieldName
     * @param \Vnecoms\PdfPro\Model\ResourceModel\Key\CollectionFactory      $pageCollectionFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface          $dataPersistor
     * @param \Vnecoms\PdfPro\Helper\Data                                    $helper
     * @param \Magento\Framework\Registry                                    $registry
     * @param array                                                          $meta
     * @param array                                                          $data
     */
    public function __construct(
        \Vnecoms\PdfPro\Model\ResourceModel\Template\CollectionFactory $collectionFactory,
        $name,
        $primaryFieldName,
        $requestFieldName = '',
        CollectionFactory $pageCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Vnecoms\PdfPro\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->templateCollectionFactory = $collectionFactory;
        $this->collection = $pageCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->helper = $helper;
        $this->registry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getThemeData()
    {
        $collection = $this->templateCollectionFactory->create();
        $result = [];
        foreach ($collection as $theme) {
            $result[$theme->getId()] = $theme->getData();
        }

        return $result;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $key = $this->registry->registry('current_key');
        if ($key) {
            $keyData = $key->getData();
//            if (isset($keyData['logo'])) {
//                unset($keyData['logo']);
//                $keyData['logo'][0]['name'] = $key->getData('api_key');
//                $keyData['logo'][0]['url'] = $this->helper->getBaseUrlMedia('ves_pdfpro/logos/'.$key->getData('logo'));
//            }

            if (isset($keyData['font_data'])) {
                $keyData['font_data'] = unserialize($keyData['font_data']);
            } else {
                $keyData['font_data'] = [];
            }
            $keyData['theme'] = $this->getThemeData();
            $this->loadedData[$key->getId()] = $keyData;
        }

        return $this->loadedData;
    }
}

