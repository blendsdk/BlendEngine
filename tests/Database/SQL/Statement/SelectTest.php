<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Database\SQL\Statement;

use Blend\Tests\Database\DatabaseTestBase;
use Blend\Component\Database\SQL\Statement\Select;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectTest extends DatabaseTestBase {

    public function testSimpleSelect() {
        $s = new Select();
        $s->from('table1')
                ->select('col1')
                ->select('col2');
        $this->assertEquals('SELECT col1, col2 FROM table1', $s);
    }

    public function testSimpleSelectWithAlias() {
        $s = new Select();
        $s->from(sqlstr('table1')->tableAlias('t1'))
                ->select(sqlstr('col1')->dotPrefix('t1'))
                ->select(sqlstr('col2')->dotPrefix('t1'));
        $this->assertEquals('SELECT t1.col1, t1.col2 FROM table1 t1', $s);
    }

    public function testSimpleSelectMultiFrom() {
        $s = new Select();
        $s
                ->from(sqlstr('table1')->tableAlias('t1'))
                ->from(sqlstr('table2')->tableAlias('t2'))
                ->select(sqlstr('col1')->dotPrefix('t1'))
                ->select(sqlstr('col1')->dotPrefix('t2'));
        $this->assertEquals('SELECT t1.col1, t2.col1 FROM table1 t1, table2 t2', $s);
    }

    public function testInnerJoin1() {
        $s = new Select();
        $s
                ->from(sqlstr('table1')->tableAlias('t1'))
                ->innerJoin(sqlstr('table2')->tableAlias('t2'), sql_join(sqlstr('id')->dotPrefix('t1'), sqlstr('id')->dotPrefix('t2')))
                ->select('*');
        $this->assertEquals('SELECT * FROM table1 t1 INNER JOIN table2 t2 ON t1.id = t2.id', $s . '');
    }

}
