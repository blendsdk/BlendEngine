<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\SQL;

use Blend\Tests\Component\Database\DatabaseTestBase;

class SQLStringTest extends DatabaseTestBase {

    public function testCast() {
        $this->assertEquals('a::varchar', sqlstr('a')->cast('varchar'));
    }

    public function testAliasTable() {
        $this->assertEquals('table1 t1', sqlstr('table1')->tableAlias('t1'));
    }

    public function testDotPrefix() {
        $this->assertEquals('t1.field', sqlstr('field')->dotPrefix('t1'));
    }

    public function testAliasColumn() {
        $this->assertEquals('col1 AS column1', sqlstr('col1')->columnAlias('column1'));
    }

    public function testCastAliasColumn() {
        $this->assertEquals('t1.col1::uuid AS col_uuid', sqlstr('col1')->cast('uuid')->columnAlias('col_uuid')->dotPrefix('t1'));
    }

    public function testTableAlias() {
        $sql = "select * from " . sqlstr('table1')->tableAlias('t1');
        $this->assertEquals('select * from table1 t1', $sql);
    }

    public function testMD5() {
        $this->assertEquals('md5(col1)', sqlstr('col1')->md5());
    }

    public function testConcat() {
        $this->assertEquals("md5(username||password)", sqlstr('username')->concat('password')->md5());
    }

}
