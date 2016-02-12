<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database;

use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\Component\Database\StatementResult;

/**
 * DatabaseDMLTest
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseDMLTest extends DatabaseTestBase {

    public static function getTestingDatabaseConfig() {
        return [
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => 'database_dml_test'
        ];
    }

    public function testInsert() {
        $qr = new \Blend\Component\Database\StatementResult();

        $result = self::$currentDatabase->insert('table1', [
            'field1' => 'data1',
            'field2' => 100
                ], $qr);

        $this->assertEquals(1, $qr->getAffectedRecords());
        $this->assertEquals(1, $result[0]['id']);
    }

    public function testUpdate() {
        for ($a = 0; $a != 10; $a++) {
            self::$currentDatabase->insert('table2', ['field1' => 'f1' . $a, 'field2' => $a]);
        }
        $sr = new StatementResult();
        self::$currentDatabase->update('table2', ['field2' => 1000], sqlstr('id')->equalsTo(':p1'), [':p1' => 2], $sr);
        $updateCounts = self::$currentDatabase->executeScalar('select count(*) from table2 where field2=1000');
        $this->assertEquals(1, $updateCounts);
        $this->assertEquals(1, $sr->getAffectedRecords());
    }

    public static function setUpSchema() {
        self::$currentDatabase->executeScript(file_get_contents(__DIR__ . '/scripts/schema.sql'));
    }

}
