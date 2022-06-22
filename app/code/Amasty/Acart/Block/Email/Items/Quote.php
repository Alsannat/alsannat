<?php

namespace Amasty\Acart\Block\Email\Items;

class Quote extends \Magento\Framework\View\Element\Template
{
    /**
     * @return array
     */
    public function getItems()
    {
        return $this->getQuote()->getAllVisibleItems();
    }
}
