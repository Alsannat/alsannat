<?php

namespace Amasty\Acart\Cron;

class ClearHistory
{
    /**
     * @var \Amasty\Acart\Model\Cleaner
     */
    private $cleaner;

    public function __construct(
        \Amasty\Acart\Model\Cleaner $cleaner
    ) {
        $this->cleaner = $cleaner;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $this->cleaner->clearExpiredHistory()->clearExpiredRuleQuotes();

        return $this;
    }
}
