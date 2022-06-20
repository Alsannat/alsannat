<?php

namespace Ced\City\Model\Config\Source;

class City implements \Magento\Framework\Data\OptionSourceInterface {

    protected $_collectionFactory;

    protected $_options;

    public function __construct(
        \Ced\City\Model\ResourceModel\City\CollectionFactory $collectionFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray() {
        if ($this->_options === null) {
            $collection = $this->_collectionFactory->create();
            $collection->setOrder('city', 'asc');
            $this->_options = [['label' => '', 'value' => '']];

            foreach ($collection as $category) {
                $this->_options[] = [
                    'label' => __($category->getCity()),
                    'value' => $category->getCityCode()
                ];
            }
        }

        return $this->_options;
    }
}