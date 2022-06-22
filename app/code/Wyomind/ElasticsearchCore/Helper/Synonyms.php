<?php
/**
 * Created by PhpStorm.
 * User: Paul
 * Date: 13/05/2019
 * Time: 14:25
 */

namespace Wyomind\ElasticsearchCore\Helper;


/**
 * Class Synonyms
 * @package Wyomind\ElasticsearchCore\Helper
 */
class Synonyms
{

    /**
     * @var mixed|string
     */
    private $path = "";

    /**
     * Synonyms constructor.
     */
    public function __construct()
    {
        $path = BP . '/var/wyomind/elasticsearch/synonyms/';
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    }

    /**
     * @param $q
     * @param $storeCode
     * @return array
     */
    public function buildSynonymsPhrases($q, $storeCode)
    {
        $terms = explode(' ', $q);

        $result = [];
        $count = count($terms);
        for ($i = 0; $i < $count; $i++) {
            $current = [$terms[$i]];
            $result[] = implode(' ', $current);
            for ($j = $i + 1; $j < $count; $j++) {
                $current[] = $terms[$j];
                $result[] = implode(' ', $current);
            }
        }

        $synonyms = $this->getSynonyms($storeCode, $result);

        $phrases = [implode(" ", $terms)];

        foreach ($synonyms as $word => $wordSynonyms) {
            foreach ($phrases as $phrase) {
                foreach ($wordSynonyms as $synonym) {
                    if ($synonym !== $word) {
                        $phrases[] = preg_replace("/\b" . $word . "\b/i", $synonym, $phrase);
                    }
                }
            }
        }
        return array_unique($phrases);
    }

    /**
     * @param $storeCode
     * @param $terms
     * @return array
     */
    public function getSynonyms($storeCode, $terms)
    {
        $filepath = $this->path . $storeCode . ".json";
        if (file_exists($filepath)) {
            $allSynonyms = json_decode(file_get_contents($filepath), true);
            $selectedSynonyms = [];
            foreach ($terms as $term) {
                if (isset($allSynonyms[$term])) {
                    $selectedSynonyms[$term] = $allSynonyms[$term];
                }
            }
            return $selectedSynonyms;
        } else {
            return [];
        }
    }

    /**
     * @param $storeCode
     * @param $synonyms
     * @throws \Exception
     */
    public function generateSynonymsFiles($storeCode, $synonyms)
    {
        $filepath = $this->path;
        $filename = $storeCode . ".json";
        if (!is_dir($filepath)) {
            try {
                mkdir($filepath, 0777, true);
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('Cannot create the folder to store the elasticsearch configuration'));
            }
        }
        file_put_contents($filepath . '/' . $filename, json_encode($synonyms));
    }

}