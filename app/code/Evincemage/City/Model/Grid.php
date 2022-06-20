<?php
namespace Evincemage\City\Model;

class Grid extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Evincemage\City\Model\ResourceModel\Grid');
    }
}
