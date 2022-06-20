<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Cat;

use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException;
use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Snapshots
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Cat
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class Snapshots extends AbstractEndpoint
{
    private $repository;

    public function setRepository(?string $repository): Snapshots
    {
        if (isset($repository) !== true) {
            return $this;
        }

        $this->repository = $repository;

        return $this;
    }

    public function getURI(): string
    {
        $repository = $this->repository ?? null;
        if (isset($repository)) {
            return "/_cat/snapshots/$repository";
        }

        return "/_cat/snapshots";
    }

    public function getParamWhitelist(): array
    {
        return [
            'format',
            'ignore_unavailable',
            'master_timeout',
            'h',
            'help',
            's',
            'v'
        ];
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
