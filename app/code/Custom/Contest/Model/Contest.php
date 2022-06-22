<?php

namespace Custom\Contest\Model;

/**
 * Contest Model
 *
 * @method \Custom\Contest\Model\Resource\Page _getResource()
 * @method \Custom\Contest\Model\Resource\Page getResource()
 */
class Contest extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Contest\Model\ResourceModel\Contest');
    }

}
