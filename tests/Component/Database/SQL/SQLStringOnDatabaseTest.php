<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\SQL;

use Blend\Component\Database\SQL\SQLString;
use Blend\Component\Database\SQL\Statement\SelectStatement;
use Blend\Tests\Component\Database\DatabaseTestBase;

/**
 * Description of SQLStringOnDatabaseTest.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SQLStringOnDatabaseTest extends DatabaseTestBase
{
    //insert into table1(col1) values(E'a');
//insert into table1(col1) values(E'a\na');
//insert into table1(col1) values(E'a\ra');
//insert into table1(col1) values(E'a\ta');
//insert into table1(col1) values(E'a\\a');
//insert into table1(col1) values(E'a''a');
//insert into table1(col1) values(E'a"a');

    public function testLists()
    {
        $list = array(
            'a',
            "a\na",
            "a\ra",
            "a\ta",
            'a\\a',
            "a\'a",
            'a"a',
        );
        $q = new SelectStatement();
        $q->from('table1')
                ->selectCount()
                ->where(sqlstr('col1')->inList($list, SQLString::STRING_RENDERER()));
        $count = self::$currentDatabase->executeScalar($q . '');
        $this->assertEquals(count($list), $count);
    }

    public static function getTestingDatabaseConfig()
    {
        return array(
            'username' => 'postgres',
            'password' => 'postgres',
            'database' => __CLASS__,
        );
    }

    public static function setUpSchema()
    {
        self::$currentDatabase->executeScript(file_get_contents(__DIR__ . '/scripts/schema.sql'));
    }
}
