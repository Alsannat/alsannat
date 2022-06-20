<?php
/**
 * Created by PhpStorm.
 * User: qhauict13
 * Date: 26/01/2016
 * Time: 21:11
 */
namespace Magenest\GiftCard\Model\ResourceModel\Template;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'template_id';

    protected function _construct()
    {
        $this->_init('Magenest\GiftCard\Model\Template', 'Magenest\GiftCard\Model\ResourceModel\Template');
    }
    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()]);
        return $this;
    }
}
