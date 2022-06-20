<?php

namespace Vnecoms\PdfPro\Model\ResourceModel\Key;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Vnecoms\PdfPro\Model\Key', 'Vnecoms\PdfPro\Model\ResourceModel\Key');
    }
}
