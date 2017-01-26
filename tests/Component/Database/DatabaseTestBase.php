<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database;

use Blend\Component\Database\Database;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Abstract class for Database tests.
 */
abstract class DatabaseTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Database
     */
    public static $currentDatabase;

    public static function getDefaultDatabaseConfig()
    {
        return array(
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'postgres',
        );
    }

    public static function getTestingDatabaseConfig()
    {
        return null;
    }

    /**
     * Returns a Database object that connects to the test database.
     *
     * @return Database
     */
    protected function getTestDatabase()
    {
        return new Database(static::getTestingDatabaseConfig());
    }

    protected static function setTestDatabaseConfig()
    {
        return null;
    }

    public static function setUpSchema()
    {
        return;
    }

    public static function setUpBeforeClass()
    {
        $config = static::getTestingDatabaseConfig();
        if (!is_null($config)) {
            $config['database'] = str_replace(array('\\', ' '), '_', trim(strtolower($config['database'])));
            $log = new Logger($config['database']);
            $log->pushHandler(new StreamHandler(TEMP_DIR . '/' . $config['database'] . '.log', Logger::DEBUG));
            $db = new Database(static::getDefaultDatabaseConfig(), $log);
            $db->executeQuery("drop database if exists {$config['database']}");
            $db->executeQuery("create database {$config['database']}");
            static::$currentDatabase = new Database($config, $log);
            static::setUpSchema();
        }
    }
}
