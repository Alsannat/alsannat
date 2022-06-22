<?php

declare(strict_types = 1);

namespace Wyomind\ElasticsearchCore\Elasticsearch\Namespaces;

use Wyomind\ElasticsearchCore\Elasticsearch\Endpoints\Remote\Info;

/**
 * Class RemoteNamespace
 *
 * @category Wyomind\Elasticsearch
 * @package  Wyomind\ElasticsearchCore\Elasticsearch\Namespaces\TasksNamespace
 * @author   Zachary Tong <zach@elastic.co>
 * @license  http://www.apache.org/licenses/LICENSE-2.0 Apache2
 * @link     http://elastic.co
 */
class RemoteNamespace extends AbstractNamespace
{
    /**
     * @return callable|array
     */
    public function info(array $params = [])
    {
        /**
 * @var callable $endpointBuilder
*/
        $endpointBuilder = $this->endpoints;

        /**
 * @var Info $endpoint
*/
        $endpoint = $endpointBuilder('Remote\Info');
        $endpoint->setParams($params);

        return $this->performRequest($endpoint);
    }
}
