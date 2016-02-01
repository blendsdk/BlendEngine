<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\SQL\Statement;

use Blend\Component\Database\SQL\SQLString;

/**
 * The Select class is a utility class to help generating a SELECT statement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Select {

    protected $columns;
    protected $from;
    private $lastFromIndex;

    public function __construct() {
        $this->columns = [];
        $this->from = [];
        $this->lastFromIndex = null;
    }

    /**
     *
     * @param type $column
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function select($column) {
        $this->columns[] = $column;
        return $this;
    }

    /**
     *
     * @param type $table
     * @param type $alias
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function from($table, $alias = '') {
        if (!empty($alias)) {
            $this->from[] = sqlstr($table)->tableAlias($alias);
        } else {
            $this->from[] = $table;
        }
        $this->lastFromIndex = count($this->from) - 1;
        return $this;
    }

    /**
     *
     * @param type $table
     * @param array $sql_join
     * @return \Blend\Component\Database\SQL\Statement\Select
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function innerJoin($table, array $sql_join) {
        if (is_null($this->lastFromIndex)) {
            throw new \LogicException("Unable to perform a join because no prior 'from' statement");
        }
        if (count($sql_join) === 0) {
            throw new \InvalidArgumentException('Invalid $sql_join parameter. Try to use sql_join(...)');
        }

        // wrap a single join in an array
        if ($sql_join[0] === '=') {
            $sql_join = [$sql_join];
        }

        $j = [];
        foreach ($sql_join as $item) {
            $j[] = "{$item[1]} {$item[0]} {$item[2]}";
        }

        $this->from[$this->lastFromIndex] .= ' INNER JOIN ' . $table . ' ON ' . implode(' AND ', $j);
        return $this;
    }

    public function __toString() {
        return 'SELECT '
                . implode(', ', $this->columns) . ' FROM '
                . implode(', ', $this->from);
    }

}
