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
 * Description of InsertStatement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class InsertStatement extends Statement {

    protected $insert_columns;

    protected abstract function getInsertColumns();

    public function __construct() {
        parent::__construct();
        $this->insert_columns = $this->getInsertColumns();
    }

    protected function buildSQL() {
        $params = array_keys($this->stmt_params);
        return "INSERT INTO {$this->table_name} (" .
                implode(', ', $this->insert_columns) .
                " ) values(" . implode(', ', $params) . ") RETURNING *";
    }

}
