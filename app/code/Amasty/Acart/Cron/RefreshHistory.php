<?php

namespace Amasty\Acart\Cron;

class RefreshHistory
{
    /**
     * @var \Amasty\Acart\Model\Indexer
     */
    private $indexer;

    public function __construct(
        \Amasty\Acart\Model\Indexer $indexer
    ) {
        $this->indexer = $indexer;
    }

    public function execute()
    {
        $this->indexer->run();
    }
}
