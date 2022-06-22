<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Snapshot;

use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;
use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;

/**
 * Class Status
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Snapshot
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class Status extends AbstractEndpoint
{
    /**
     * A comma-separated list of repository names
     *
     * @var string
     */
    private $repository;

    /**
     * A comma-separated list of snapshot names
     *
     * @var string
     */
    private $snapshot;

    public function setRepository(?string $repository): Status
    {
        if (isset($repository) !== true) {
            return $this;
        }

        $this->repository = $repository;

        return $this;
    }

    public function setSnapshot(?string $snapshot): Status
    {
        if (isset($snapshot) !== true) {
            return $this;
        }

        $this->snapshot = $snapshot;

        return $this;
    }

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getURI(): string
    {
        $repository = $this->repository ?? null;
        $snapshot   = $this->snapshot ?? null;

        if (isset($snapshot) && isset($repository)) {
            return "/_snapshot/$repository/$snapshot/_status";
        }
        if (isset($repository)) {
            return "/_snapshot/$repository/_status";
        }
        return "/_snapshot/_status";
    }

    public function getParamWhitelist(): array
    {
        return [
            'master_timeout',
            'ignore_unavailable'
        ];
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
