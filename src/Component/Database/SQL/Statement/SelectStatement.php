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

/**
 * The Select class is a utility class to help generating a SELECT statement.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SelectStatement extends ConditionableStatement
{
    /**
     * Array containing the select columns.
     *
     * @var array
     */
    protected $columns;

    /**
     * Array containg the FROM tables.
     *
     * @var array
     */
    protected $from;

    /**
     * Array containing the ORDER BY columns.
     *
     * @var array
     */
    protected $orderby;

    /**
     * Array containing the GROUP BY columns.
     *
     * @var array
     */
    protected $groupBy;

    /**
     * Array containing the HAVING conditions.
     *
     * @var type
     */
    protected $groupByHaving;

    /**
     * Index of the last inserted FROM column. This is used to help place
     * a JOIN on a correct table.
     *
     * @var int
     */
    private $lastFromIndex;

    public function __construct()
    {
        $this->lastFromIndex = null;
        $this->columns = array();
        $this->from = array();
        $this->where = array();
        $this->orderby = array();
        $this->groupBy = array();
        $this->groupByHaving = array();
    }

    /**
     * Adds a count(..) to the columns list.
     *
     * @param string $alias  An optional alias to set on the column,
     *                       for example: count(*) as count_of
     * @param string $column an optional column name. In case nothing is
     *                       provided it will be set to '*'
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function selectCount($alias = '', $column = null)
    {
        $count = sqlstr(empty($column) ? '*' : $column)->count();

        return $this->select(empty($alias) ? $count : $count->columnAlias($alias));
    }

    /**
     * Adds the '*' to the SELECT columns.
     *
     * @param type $prefix The option to prefix (filter) out the unwanted
     *                     columns
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function selectAll($prefix = '')
    {
        if (!empty($prefix)) {
            $this->select("{$prefix}.*");
        } else {
            return $this->select('*');
        }
    }

    /**
     * Adds a column to SELECT columns.
     *
     * @param string $column
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function select($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * Adds a table an a optional alias to the FROM list.
     *
     * @param string $table The name of the table
     * @param string $alias The table alias, default to ''
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function from($table, $alias = '')
    {
        if (!empty($alias)) {
            $this->from[] = sqlstr($table)->tableAlias($alias);
        } else {
            $this->from[] = $table;
        }
        $this->lastFromIndex = count($this->from) - 1;

        return $this;
    }

    /**
     * Adds a JOIN to the last inserted FROm table. Use the sql_join(...)
     * funtion to create the JOIN conditions.
     *
     * @param string $table    The name of the table to join to
     * @param array  $sql_join An array of JOIN conditions. Use sql_join(...)
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function innerJoin($table, array $sql_join)
    {
        return $this->join('INNER JOIN', $table, $sql_join);
    }

    /**
     * Interval method for handling joins.
     *
     * @param type  $type
     * @param type  $table
     * @param array $sql_join
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     *
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    private function join($type, $table, array $sql_join)
    {
        if (is_null($this->lastFromIndex)) {
            throw new \LogicException("Unable to perform a join because no prior 'from' statement");
        }
        if (count($sql_join) === 0) {
            throw new \InvalidArgumentException('Invalid $sql_join parameter. Try to use sql_join(...)');
        }

        // wrap a single join in an array
        if ($sql_join[0] === '=') {
            $sql_join = array($sql_join);
        }

        $j = array();
        foreach ($sql_join as $item) {
            $j[] = "{$item[1]} {$item[0]} {$item[2]}";
        }

        $this->from[$this->lastFromIndex] .= ' '.$type.' '.$table.' ON '.implode(' AND ', $j);

        return $this;
    }

    /**
     * Adds a column to the ORDER BY list.
     *
     * @param string $column
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function orderBy($column)
    {
        $this->orderby[] = $column;

        return $this;
    }

    /**
     * Adds a column to the GROUP BY list.
     *
     * @param string $column
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function groupBy($column)
    {
        $this->groupBy[] = $column;

        return $this;
    }

    /**
     * Adds a condition to the HAVING cluase.
     *
     * @param type $condition
     *
     * @return \Blend\Component\Database\SQL\Statement\Select
     */
    public function groupByHaving($condition)
    {
        $this->groupByHaving[] = $condition;

        return $this;
    }

    /**
     * Renders the column list if not column is select then we will select all
     * column by returning '*'.
     *
     * @return string
     */
    private function getColumns()
    {
        if (count($this->columns) !== 0) {
            return implode(', ', $this->columns);
        } else {
            return '*';
        }
    }

    /**
     * Renders the FROM list.
     *
     * @return string
     */
    private function getForm()
    {
        if (count($this->from) !== 0) {
            return ' FROM '.implode(', ', $this->from);
        } else {
            return '';
        }
    }

    /**
     * Renders the ORDER BY list.
     *
     * @return string
     */
    private function getOrderBy()
    {
        if (count($this->orderby) !== 0) {
            return ' ORDER BY '.implode(', ', $this->orderby);
        } else {
            return '';
        }
    }

    /**
     * Renders the GROUP BY list.
     *
     * @return string
     */
    private function getGroupBy()
    {
        if (count($this->groupBy) !== 0) {
            return ' GROUP BY '.implode(', ', $this->groupBy);
        } else {
            return '';
        }
    }

    public function __toString()
    {
        return 'SELECT '
                .$this->getColumns()
                .$this->getForm()
                .$this->getWhereClause()
                .$this->getGroupBy()
                .$this->getOrderBy();
    }
}
