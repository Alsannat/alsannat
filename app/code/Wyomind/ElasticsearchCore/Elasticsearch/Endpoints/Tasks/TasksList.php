<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Tasks;

use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;
use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class List
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Tasks
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class TasksList extends AbstractEndpoint
{

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getURI(): string
    {
        return "/_tasks";
    }

    public function getParamWhitelist(): array
    {
        return [
            'nodes',
            'actions',
            'detailed',
            'parent_task_id',
            'wait_for_completion',
            'group_by',
            'timeout'
        ];
    }

    public function getMethod(): string
    {
        return 'GET';
    }
}
