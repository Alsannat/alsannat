<?php

/**
 * Contest Resource Collection
 */
namespace Custom\Contest\Model\ResourceModel\Contest;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Contest\Model\Contest', 'Custom\Contest\Model\ResourceModel\Contest');
    }
}
