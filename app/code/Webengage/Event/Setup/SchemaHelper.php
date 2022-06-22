<?php

namespace Webengage\Event\Setup;


class SchemaHelper {
    private $tableName = 'webengage_configuration';

    public function __construct() {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->resource = $this->objectManager->get('Magento\Framework\App\ResourceConnection');
        $this->connection = $this->resource->getConnection();
    }

    public function createTableIfNotExists($installer) {
        if ($installer->tableExists($this->tableName)) {
            return;
        }

        // CREATE TABLE
        $table = $installer->getConnection()->newTable(
                $installer->getTable($this->tableName)
            );

        $table->addColumn(
                'weid',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ]
            );

        $table->addColumn(
                'wekey',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [
                    'nullable' => true,
                    'default' => null,
                ]
            );

        $table->addColumn(
                'wevalue',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                100,
                [
                    'nullable' => true,
                    'default' => null,
                ]
            );

        $installer->getConnection()->createTable($table);
    }

    public function createAllConfigsIfNotExists($installer) {
        $this->createConfigIfNotExists('we_licence_code');
        $this->createConfigIfNotExists('we_api_key');
        $this->createConfigIfNotExists('we_debug');
        $this->createConfigIfNotExists('we_region', 'us');
    }

    public function createConfigIfNotExists($key, $defaultValue='') {
        if (!$this->doesConfigExist($key)) {
            $this->createConfig($key, $defaultValue);
        }
    }

    public function doesConfigExist($key) {
        // Check if key exists
        $checkData = 'SELECT * FROM `webengage_configuration` WHERE `wekey` = ? limit 1';
        $getData = $this->connection->fetchAll($checkData, [0 => $key]);
        
        return !empty($getData);
    }

    public function createConfig($key, $defaultValue) {
        $this->connection->query("INSERT INTO $this->tableName (wekey, wevalue) VALUES (?, ?)", [0 => $key, 1 => $defaultValue]);
    }
}

