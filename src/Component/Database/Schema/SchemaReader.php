<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Schema;

use Blend\Component\Database\Database;
use Blend\Component\Database\SQL\Statement\Select;
use Blend\Component\Database\SQL\SQLString;

/**
 * Read the database schema from a PostgreSQL Database for code 
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaReader {

    /**
     * Instance of a Database
     * @var Database 
     */
    protected $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    public function load() {
        $this->readSchemas();
    }

    protected function readSchemas() {
        $skip = ['pg_toast', 'pg_temp_1', 'pg_toast_temp_1', 'pg_catalog', 'information_schema'];
        $q = new Select();
        $q->from($this->informationSchema('schemeta'))
        ->selectAll()
        ->where(sqlstr('schema_name')->notInList($skip,  function($list){
            return SQLString::arrayAsStrings($list);
        }));
    }

    protected function informationSchema($table) {
        return "information_schema.{$table}";
    }

}
