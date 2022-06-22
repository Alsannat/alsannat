<?php
/**
 * Created by Magenest
 * User: Luu Thanh Thuy
 * Date: 27/01/2016
 * Time: 09:25
 */

namespace Magenest\GiftCard\Model;

class Art extends \Magento\Framework\Model\AbstractModel
{

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\ResourceModel\Art');
    }
}
