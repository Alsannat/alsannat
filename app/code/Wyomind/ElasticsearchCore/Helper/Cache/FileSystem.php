<?php

namespace Wyomind\ElasticsearchCore\Helper\Cache;

class FileSystem extends \Wyomind\ElasticsearchCore\Helper\Cache\AbstractCache
{


    protected $cachePath = BP . "/var/wyomind/elasticsearch/cache/";

    public function __construct()
    {

    }

    public function put($key, $value)
    {
//        $filepath = $this->cachePath . $key[0] . '/' . $key[1] . '/' . $key[2] . '/';
//        $filename = $key;
//        if (!is_dir($filepath)) {
//            mkdir($filepath, 0644, true);
//        }
//        file_put_contents($filepath . '/' . $filename, json_encode($value));
    }

    public function get($key)
    {
//        $filePath = $this->cachePath . $key[0] . '/' . $key[1] . '/' . $key[2] . '/' . $key;
//        if (file_exists($filePath)) {
//            return json_decode(file_get_contents($filePath),true);
//        } else {
            return null;
//        }
    }
}