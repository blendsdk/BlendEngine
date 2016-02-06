<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Tests\Component\Database\SQL\Statement;

use Blend\Tests\Component\Database\DatabaseTestBase;
use Blend\Component\Database\SQL\Statement\SelectStatement;

/**
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectOrderAndGroupTest extends DatabaseTestBase {

    public function testOrderBy() {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->orderBy('field1')
                ->orderBy('field2')
                ->selectAll();
        $this->assertEquals('SELECT * FROM table1 ORDER BY field1, field2', $s . '');
    }

    public function testGroupBy() {
        $s = new SelectStatement();
        $s
                ->from('table1')
                ->selectCount(null, 'field1')
                ->select('field1')
                ->groupBy('field1')
                ->groupBy('field2');
        $this->assertEquals('SELECT COUNT(field1), field1 FROM table1 GROUP BY field1, field2', $s . '');
    }

}
