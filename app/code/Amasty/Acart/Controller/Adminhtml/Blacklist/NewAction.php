<?php
declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Blacklist;

use Amasty\Acart\Controller\Adminhtml\Blacklist;

class NewAction extends Blacklist
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
