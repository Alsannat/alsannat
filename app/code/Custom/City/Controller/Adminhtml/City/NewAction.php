<?php

namespace Custom\City\Controller\Adminhtml\City;

use Custom\City\Controller\Adminhtml\City;

class NewAction extends City
{
    /**
     * Create new city action
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
 