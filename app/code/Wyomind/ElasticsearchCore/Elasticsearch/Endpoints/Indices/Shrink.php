<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Indices;

use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\AbstractEndpoint;
use Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;

/**
 * Class Shrink.
 *
 * @category Wyomind\Elasticsearch
 *
 * @author  Zachary Tong <zach@elastic.co>
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache2
 *
 * @link http://elastic.co
 */
class Shrink extends AbstractEndpoint
{
    /**
     * The name of the target index to shrink into
     *
     * @var string
     */
    private $target;

    public function setBody($body): Shrink
    {
        if (isset($body) !== true) {
            return $this;
        }

        $this->body = $body;

        return $this;
    }

    public function setTarget(?string $target): Shrink
    {
        if (isset($target) !== true) {
            return $this;
        }
        $this->target = $target;

        return $this;
    }

    /**
     * @throws \Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions\RuntimeException
     */
    public function getURI(): string
    {
        if (isset($this->index) !== true) {
            throw new Exceptions\RuntimeException(
                'index is required for Shrink'
            );
        }
        if (isset($this->target) !== true) {
            throw new Exceptions\RuntimeException(
                'target is required for Shrink'
            );
        }
        return "/{$this->index}/_shrink/{$this->target}";
    }

    public function getParamWhitelist(): array
    {
        return [
            'copy_settings',
            'timeout',
            'master_timeout',
            'wait_for_active_shards'
        ];
    }

    public function getMethod(): string
    {
        return 'PUT';
    }
}
