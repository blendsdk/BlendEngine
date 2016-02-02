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
 * Description of WhereClause
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
trait WhereClause {

    protected $where;

    /**
     * @param type $condition
     * @return WhereClause
     */
    public function where($condition) {
        $this->where[] = $condition;
        return $this;
    }

    /**
     * @param type $condition
     * @return type
     */
    public function andWhere($condition) {
        $this->where[] = 'AND ' . $condition;
        return $this;
    }

    /**
     * @param type $condition
     * @return WhereClause
     */
    public function orWhere($condition) {
        $this->where[] = 'OR ' . $condition;
        return $this;
    }

    /**
     *
     * @return string
     */
    protected function getWhereClause() {
        if (count($this->where) !== 0) {
            return ' WHERE ' . implode(' ', $this->where);
        } else {
            return '';
        }
    }

    /**
     *
     * @return WhereClause
     */
    public function whereScope() {
        $this->where[] = '(';
        return $this;
    }

    /**
     *
     * @return WhereClause
     */
    public function endWhereScope() {
        $this->where[] = ')';
        return $this;
    }

}
