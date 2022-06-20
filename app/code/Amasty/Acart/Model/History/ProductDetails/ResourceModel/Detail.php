<?php

declare(strict_types=1);

namespace Amasty\Acart\Model\History\ProductDetails\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Detail extends AbstractDb
{
    const TABLE_NAME = 'amasty_acart_history_details';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, \Amasty\Acart\Model\History\ProductDetails\Detail::DETAIL_ID);
    }
}
