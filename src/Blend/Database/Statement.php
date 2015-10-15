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

/**
 * Base class for a database Statement
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Statement {

    protected $sql;
    protected $table_name;
    protected $stmt_params;

    protected abstract function buildSQL();

    public function __construct() {
        $this->sql = null;
        $this->stmt_params = array();
    }

    public function getParameters() {
        return $this->stmt_params;
    }

    protected function setParameterValue($name, $value) {
        $this->stmt_params[$this->param($name)] = $this->parseValue($value);
    }

    public function getSQL() {
        if (is_null($this->sql)) {
            $this->sql = $this->buildSQL();
        }
        return $this->sql;
    }

    protected function param($name) {
        return ":$name";
    }

    /**
     * Parses the PHP values to a corresponding database fromat
     * @param mixed $value
     * @return mixed
     */
    protected function parseValue($value) {
        if (is_bool($value)) {
            return $value === true ? 'true' : 'false';
        } else if (is_null($value)) {
            return 'null';
        } else {
            return $value;
        }
    }

}
