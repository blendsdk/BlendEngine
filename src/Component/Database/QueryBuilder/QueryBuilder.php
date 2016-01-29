<?php

namespace Blend\Component\Database\QueryBuilder;

use Blend\Component\Database\QueryBuilder\SQL;

class QueryBuilder extends SQL {

    protected $fields;
    protected $tables;
    protected $joins;
    protected $statement;

    const STMT_SELECT = 'SELECT';
    const SQL_FROM = 'FROM';
    const JOIN_EQUAL = '=';

    public function __construct() {
        $this->tables = array();
        $this->joins = array();
        $this->fields = array();
    }

    /**
     * Compiles the query to string
     * @return string
     */
    public function compile() {
        $sql = array();
        if ($this->statement == self::STMT_SELECT) {
            $sql = array(
                self::STMT_SELECT,
                trim(implode(',', $this->fields)),
                self::SQL_FROM,
                trim(implode(',', $this->tables)),
                trim(implode(' ', $this->joins))
            );
        }
        return trim(implode(' ', array_filter($sql)));
    }

    /**
     * Adds * to SELECT
     * @param string $alias
     * @return QueryBuilder
     */
    public function selectAll($alias = null) {
        return $this->select(empty($alias) ? '*' : $alias . '.*');
    }

    /**
     * Adds a field to SELECT, optionalliy with a alias
     * @param string/['alias' => 'field'] $field
     * @param type $type cast to type
     * @return QueryBuilder
     */
    public function select($field, $type = null) {
        $as = null;
        if (is_array($field)) {
            $keys = array_keys($field);
            $as = " as {$keys[0]}";
            $field = self::field($field[$keys[0]]);
        }
        $type = empty($type) ? '' : "::{$type}";
        $this->fields[] = trim("{$field}{$type}{$as}");
        $this->statement = self::STMT_SELECT;
        return $this;
    }

    /**
     * Adds a table to the SELECT query
     * @param string/['alias' => 'table'] $table_name
     * @return QueryBuilder
     */
    public function from($table_name) {
        $this->tables[] = self::tableAlias($table_name);
        return $this;
    }

    /**
     * Adds a join to a SELECT statament
     * @param string/['alias' => 'table'] $fieldLeft
     * @param string/['alias' => 'table'] $fieldRight
     * @param string $joinOpr join operator, defaults to '='
     * @return QueryBuilder
     */
    protected function joinOn($fieldLeft, $fieldRight, $joinOpr = null) {
        if (empty($joinOpr)) {
            $joinOpr = self::JOIN_EQUAL;
        }
        return trim(implode(' ', array(
            self::field($fieldLeft),
            $joinOpr,
            self::field($fieldRight)
        )));
    }

    /**
     * Adds an INNER JOIN to the query
     * @param string/['alias' => 'table'] $table
     * @param array $joinFields
     * @return QueryBuilder
     */
    public function innerJoin($table, array $joinFields) {
        return $this->join('INNER', $table, $joinFields);
    }

    /**
     * @internal
     * @return \Blend\Database\QueryBuilder\QueryBuilder
     */
    protected function join($type, $table_name, array $joinFields) {
        $table = self::tableAlias($table_name);
        $jf = array();
        foreach ($joinFields as $item) {
            $jf[] = call_user_func_array(array($this, 'joinOn'), $item);
        }
        $this->joins[] = "{$type} JOIN {$table} ON " . trim(implode(' AND ', $jf));
        return $this;
    }

}
