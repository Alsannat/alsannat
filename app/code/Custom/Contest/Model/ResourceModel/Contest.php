<?php

namespace Custom\Contest\Model\ResourceModel;

/**
 * Contest Resource Model
 */
class Contest extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('custom_contest', 'contest_id');
    }
}
