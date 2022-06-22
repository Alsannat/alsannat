<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions;

/**
 * RequestTimeout408Exception
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Common\Exceptions
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class RequestTimeout408Exception extends BadRequest400Exception implements Exception
{
}
