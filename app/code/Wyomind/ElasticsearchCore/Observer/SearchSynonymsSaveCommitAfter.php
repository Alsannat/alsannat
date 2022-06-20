<?php

namespace Wyomind\ElasticsearchCore\Observer;


use Magento\Framework\Event\Observer;

class SearchSynonymsSaveCommitAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $object = $observer->getEvent()->getData("object");
    }
}