<?php

namespace Wyomind\ElasticsearchCore\Helper\Cache;

abstract class AbstractCache
{

    public abstract function put($key, $value);

    public abstract function get($key);

}