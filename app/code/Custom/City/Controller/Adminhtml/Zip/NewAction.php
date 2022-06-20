<?php

namespace Custom\City\Controller\Adminhtml\Zip;

use Custom\City\Controller\Adminhtml\Zip;

class NewAction extends Zip
{
    /**
     * Create new Zip action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
 