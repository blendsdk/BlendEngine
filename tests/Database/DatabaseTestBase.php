<?php

namespace Blend\Tests\Database;

use Blend\Component\Database\Database;

abstract class DatabaseTestBase extends \PHPUnit_Framework_TestCase {

    public static function getDefaultDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'postgres'
        ];
    }

    public static function getTestingDatabaseConfig() {
        return null;
    }

    /**
     * Returns a Database object that connects to the test database
     * @return Database
     */
    protected function getTestDatabase() {
        return new Database(static::getTestingDatabaseConfig());
    }

    protected static function setTestDatabaseConfig() {
        return null;
    }

    public static function setUpBeforeClass() {
        $config = static::getTestingDatabaseConfig();
        if (!is_null($config)) {
            $db = new Database(static::getDefaultDatabaseConfig());
            $db->executeQuery("drop database if exists {$config['database']}");
            $db->executeQuery("create database {$config['database']}");
        }
    }

}
