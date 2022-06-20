<?php

/**
 * Copyright © 2018 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */


namespace Wyomind\ElasticsearchCore\Model\ResourceModel;

/**
 * Get the config directly from the database
 */
class Config extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    /**
     * Class internal constructor (unused, be defined because it is an abstract method)
     * @return \Wyomind\ElasticsearchCore\Model\ResourceModel\Config
     */
    public function _construct()
    {
        return $this;
    }

    /**
     * Get a config value for a path (scope default), in the database directly
     * @param string $path
     * @return string | integer
     */
    public function getDefaultValueByPath($path)
    {
        $connection = $this->getConnection();
        $result = $connection->select()
                ->from($this->getTable('core_config_data'), ['value'])
                ->where("path = ? and scope_id = 0", $path)
                ->limit(1);
        $value = $connection->fetchOne($result);
        if ($value !== false) {
            return $value;
        } else {

            return null;
        }
    }

}
