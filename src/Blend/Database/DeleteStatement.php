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
 * Description of DeleteStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class DeleteStatement extends Statement {

    protected $delete_clause;

    protected abstract function getDeleteClause();

    public function __construct($table_name) {
        parent::__construct();
        $this->table_name = $table_name;
        $this->delete_clause = $this->getDeleteClause();
    }

    protected function buildSQL() {
        $where_clause = '';
        $sql = "DELETE FROM {$this->table_name}";
        if (!empty($this->delete_clause)) {
            $where_clause = " WHERE {$this->delete_clause} ";
        }
        return "DELETE FROM {$this->table_name} {$where_clause} RETURNING *";
    }

}
