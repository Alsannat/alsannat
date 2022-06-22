<?php
 
namespace Ced\City\Model\ResourceModel\City;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    protected $_foregroundCountries = [];
    protected function _construct()
    {
        $this->_init(
            'Ced\City\Model\City',
            'Ced\City\Model\ResourceModel\City'
        );
    }
    public function setForegroundCity($foregroundCity)
    {
        if (empty($foregroundCity)) {
            return $this;
        }
        $this->_foregroundCity = (array)$foregroundCity;
        return $this;
    }
}