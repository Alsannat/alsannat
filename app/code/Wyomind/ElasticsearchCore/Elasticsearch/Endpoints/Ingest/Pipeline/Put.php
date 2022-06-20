<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Ingest\Pipeline;

use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;
use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;

/**
 * Class Put
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Ingest
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class Put extends AbstractEndpoint
{
    public function setBody(string $body): Put
    {
        if (isset($body) !== true) {
            return $this;
        }

        $this->body = $body;

        return $this;
    }

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getURI(): string
    {
        if (isset($this->id) !== true) {
            throw new Exceptions\RuntimeException(
                'id is required for PutPipeline'
            );
        }
        return "/_ingest/pipeline/{$this->id}";
    }

    public function getParamWhitelist(): array
    {
        return [
            'master_timeout',
            'timeout'
        ];
    }

    public function getMethod(): string
    {
        return 'PUT';
    }
}
