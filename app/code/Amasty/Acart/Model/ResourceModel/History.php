<?php

namespace Amasty\Acart\Model\ResourceModel;

use Amasty\Acart\Model\History as HistoryModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

class History extends AbstractDb
{
    const TABLE_NAME = 'amasty_acart_history';

    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, HistoryModel::HISTORY_ID);
    }
}
