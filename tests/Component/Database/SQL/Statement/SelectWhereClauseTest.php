<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\SQL\Statement;

use Blend\Component\Database\SQL\Statement\SelectStatement;
use Blend\Tests\Component\Database\DatabaseTestBase;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectWhereClauseTest extends DatabaseTestBase
{
    public function testWhere()
    {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->where(sqlstr('field1')->equalsTo('5'))
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 WHERE field1 = 5', $s . '');
    }

    public function testAndWhere()
    {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->where(sqlstr('field1')->equalsTo('5'))
                ->andWhere(sqlstr('field2')->equalsTo('10'))
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 WHERE field1 = 5 AND field2 = 10', $s . '');
    }

    public function testOrWhere()
    {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->where(sqlstr('field1')->equalsTo('5'))
                ->orWhere(sqlstr('field2')->equalsTo('10'))
                ->orWhere(sqlstr('field2')->equalsTo('3'))
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 WHERE field1 = 5 OR field2 = 10 OR field2 = 3', $s . '');
    }

    public function testScope()
    {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->whereScope()
                ->where(sqlstr('field1')->greaterThan('10'))
                ->orWhere(sqlstr('field1')->smallerThan('20'))
                ->endWhereScope()
                ->andWhere(sqlstr('field2')->equalsTo('5'))
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 WHERE ( field1 > 10 OR field1 < 20 ) AND field2 = 5', $s . '');
    }

    public function testNulls()
    {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->where(sqlstr('field1')->isNull())
                ->andWhere(sqlstr('field2')->isNotNull())
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 WHERE field1 IS NULL AND field2 IS NOT NULL', $s . '');
    }
}
