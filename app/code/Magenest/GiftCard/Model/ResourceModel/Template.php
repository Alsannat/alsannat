<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 26/01/2016
 * Time: 21:08
 */
namespace Magenest\GiftCard\Model\ResourceModel;

class Template extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('magenest_giftcard_template', 'template_id');
    }
}
