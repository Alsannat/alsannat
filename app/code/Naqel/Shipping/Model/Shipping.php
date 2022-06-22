<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */

namespace Naqel\Shipping\Model;

use Magento\Framework\Model\AbstractModel;

class Shipping extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Naqel\Shipping\Model\ResourceModel\Shipping');
    }
}