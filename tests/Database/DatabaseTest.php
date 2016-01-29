<?php

namespace Blend\Tests\Database;

use Blend\Component\Database\Database;
use Blend\Tests\Database\DatabaseTestBase;

class DatabaseTest extends DatabaseTestBase {

    /**
     * @expectedException \Blend\Component\Exception\InvalidConfigException
     */
    public function testDatabaseConfigError() {
        $db = new Database();
    }

    public function testDatabaseSanity() {
        $db = new Database(static::getDefaultDatabaseConfig());
    }

    public function testExecuteScript() {
        $db = new Database(static::getDefaultDatabaseConfig());
        $result = $db->executeScript([
            'select 1',
            'select 2',
            'select now()'
        ]);
        $this->assertTrue($result);
    }

    /**
     * @expectedException \Blend\Component\Exception\DatabaseQueryException
     */
    public function testExecuteQueryNoTable() {
        $db = new Database(static::getDefaultDatabaseConfig());
        $db->executeQuery("select * from table1");
    }

    public function testExecuteQuerySanity() {
        $db = new Database(static::getDefaultDatabaseConfig());
        $recordset = $db->executeQuery("select * from pg_database");

        $this->assertTrue(count($recordset) != 0);
        $this->assertArrayHasKey('datname', $recordset[0]);
    }

    public function testExecuteQueryScalar() {
        $db = new Database(static::getDefaultDatabaseConfig());
        $result = $db->executeScalar("select count(*) from pg_database where datname='postgres'");
        $this->assertEquals(1, $result);
    }

}
