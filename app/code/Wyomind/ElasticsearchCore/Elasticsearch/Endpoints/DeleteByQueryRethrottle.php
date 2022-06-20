<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints;

use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException;

/**
 * Class DeletebyqueryRethrottle
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints
 * @author   Enrico Zimuel <enrico.zimuel@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class DeleteByQueryRethrottle extends AbstractEndpoint
{
    protected $taskId;

    public function setTaskId(?string $taskId): DeleteByQueryRethrottle
    {
        if ($taskId !== null) {
            $this->taskId = $taskId;
        }
        return $this;
    }
    /**
     * @throws RuntimeException
     */
    public function getURI(): string
    {
        if (! isset($this->taskId)) {
            throw new RuntimeException(
                'task_id is required for DeleteByQueryRethrottle'
            );
        }
        return "/_delete_by_query/{$this->taskId}/_rethrottle";
    }

    public function getParamWhitelist(): array
    {
        return [
            'requests_per_second'
        ];
    }

    public function getMethod(): string
    {
        return 'POST';
    }
}
