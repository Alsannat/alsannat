<?php
/**
 * @category   Naqel
 * @package    Naqel_Shipping
 */
namespace Naqel\Shipping\Controller\Adminhtml\Items;

class NewAction extends \Naqel\Shipping\Controller\Adminhtml\Items
{

    public function execute()
    {
        $this->_forward('edit');
    }
}
