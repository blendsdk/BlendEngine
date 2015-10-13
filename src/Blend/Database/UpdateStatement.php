<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database;

use Blend\Database\Statement;

/**
 * Description of UpdateStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class UpdateStatement extends Statement {

    protected $update_columns;
    protected $update_clause;

    protected abstract function getUpdateColumns();

    protected abstract function getUpdateClause();

    public function __construct($table_name) {
        parent::__construct();
        $this->table_name = $table_name;
        $this->update_columns = $this->getUpdateColumns();
        $this->update_clause = $this->getUpdateClause();
    }

    protected function buildSQL() {
        $where_clause = '';
        $set_fields = array();
        foreach ($this->update_columns as $column) {
            $param = $this->param($column);
            $set_fields[] = "{$column} = {$param}";
        }

        if (!empty($this->update_clause)) {
            $where_clause = "WHERE $this->update_clause";
        }

        return "UPDATE {$this->table_name} SET " .
                implode(', ', $set_fields) .
                ' ' .
                $where_clause .
                ' RETURNING *';
    }

}
