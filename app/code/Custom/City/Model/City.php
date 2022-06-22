<?php
 
namespace Custom\City\Model;
 
use Magento\Framework\Model\AbstractModel;
 
class City extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Custom\City\Model\Resource\City');
    }
}