<?php
declare(strict_types=1);

namespace Amasty\Acart\Controller\Adminhtml\Rule;

use Amasty\Acart\Controller\Adminhtml\Rule;

class NewAction extends Rule
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
