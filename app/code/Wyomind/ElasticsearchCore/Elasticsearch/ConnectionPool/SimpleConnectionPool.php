<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\ConnectionPool;

use Wyomind\ElasticsearchCore\Elasticsearch\ConnectionPool\Selectors\SelectorInterface;
use Wyomind\ElasticsearchCore\Elasticsearch\Connections\Connection;
use Wyomind\ElasticsearchCore\Elasticsearch\Connections\ConnectionFactoryInterface;
use Wyomind\ElasticsearchCore\Elasticsearch\Connections\ConnectionInterface;

class SimpleConnectionPool extends AbstractConnectionPool implements ConnectionPoolInterface
{

    /**
     * {@inheritdoc}
     */
    public function __construct($connections, SelectorInterface $selector, ConnectionFactoryInterface $factory, $connectionPoolParams)
    {
        parent::__construct($connections, $selector, $factory, $connectionPoolParams);
    }

    public function nextConnection(bool $force = false): ConnectionInterface
    {
        return $this->selector->select($this->connections);
    }

    public function scheduleCheck(): void
    {
    }
}
