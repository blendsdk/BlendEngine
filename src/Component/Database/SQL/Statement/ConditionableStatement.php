<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database\SQL\Statement;

/**
 * ConditionableStatement is the base class for SELECT, UPDATE, and the DELETE
 * statements providing the ability to set add a WHERE clause to the statement.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ConditionableStatement
{
    protected $where;

    /**
     * @param type $condition
     *
     * @return \Blend\Component\Database\SQL\Statement\ConditionableStatement
     */
    public function where($condition)
    {
        $this->where[] = $condition;

        return $this;
    }

    /**
     * @param type $condition
     *
     * @return \Blend\Component\Database\SQL\Statement\ConditionableStatement
     */
    public function andWhere($condition)
    {
        $this->where[] = 'AND '.$condition;

        return $this;
    }

    /**
     * @param type $condition
     *
     * @return \Blend\Component\Database\SQL\Statement\ConditionableStatement
     */
    public function orWhere($condition)
    {
        $this->where[] = 'OR '.$condition;

        return $this;
    }

    /**
     * Retins the WHERE clause.
     *
     * @return string
     */
    protected function getWhereClause()
    {
        if (count($this->where) !== 0) {
            return ' WHERE '.implode(' ', $this->where);
        } else {
            return '';
        }
    }

    /**
     * @return \Blend\Component\Database\SQL\Statement\ConditionableStatement
     */
    public function whereScope()
    {
        $this->where[] = '(';

        return $this;
    }

    /**
     * @return \Blend\Component\Database\SQL\Statement\ConditionableStatement
     */
    public function endWhereScope()
    {
        $this->where[] = ')';

        return $this;
    }
}
