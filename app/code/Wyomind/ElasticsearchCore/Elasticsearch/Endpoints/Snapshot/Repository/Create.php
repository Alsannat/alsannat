<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Snapshot\Repository;

use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;
use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;

/**
 * Class Create
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Snapshot\Repository
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class Create extends AbstractEndpoint
{
    /**
     * A repository name
     *
     * @var string
     */
    private $repository;

    public function setBody($body): Create
    {
        if (isset($body) !== true) {
            return $this;
        }

        $this->body = $body;

        return $this;
    }

    public function setRepository(?string $repository): Create
    {
        if (isset($repository) !== true) {
            return $this;
        }

        $this->repository = $repository;

        return $this;
    }

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getURI(): string
    {
        if (isset($this->repository) !== true) {
            throw new Exceptions\RuntimeException(
                'repository is required for Create'
            );
        }
        return "/_snapshot/{$this->repository}";
    }

    public function getParamWhitelist(): array
    {
        return [
            'master_timeout',
            'timeout',
            'verify'
        ];
    }

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getBody()
    {
        if (isset($this->body) !== true) {
            throw new Exceptions\RuntimeException('Body is required for Create Repository');
        }

        return $this->body;
    }

    public function getMethod(): string
    {
        return 'PUT';
    }
}
