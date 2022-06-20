<?php

namespace Amasty\Acart\Model;

class SalesRule extends \Magento\SalesRule\Model\Rule
{
    /**
     * _construct
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Acart\Model\ResourceModel\Rule');
        $this->setIdFieldName('rule_id');
    }
}