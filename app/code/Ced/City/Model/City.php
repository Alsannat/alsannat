<?php
 
namespace Ced\City\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class City extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Ced\City\Model\ResourceModel\City');
    }
}