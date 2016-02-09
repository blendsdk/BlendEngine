<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Schema;

use Blend\DataModelBuilder\Schema\Record;
use Blend\Component\Exception\InvalidSchemaException;

/**
 * Relation represents a table of a view from a PostgreSQL database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Relation extends Record {

    /**
     * @var Column[]; 
     */
    private $columns = [];
    protected $keys = [];
    protected $keysByType = [];

    /**
     * Checks whether this relation is writable
     * @return boolean
     */
    public function writable() {
        return $this->record['table_type'] === 'BASE TABLE';
    }

    /**
     * Gets the relation name
     * @return string
     */
    public function getName($prettify = false) {
        return $this->getString('table_name', $prettify);
    }

    public function getFQRN() {
        return $this->getSchemaName() . '.' . $this->getName();
    }

    /**
     * Gets the schema name
     * @return string
     */
    public function getSchemaName($prettify = false) {
        return $this->getString('table_schema', $prettify);
    }

    /**
     * Gets the list of the columns in this Relation
     * @return Column[]
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * Gets the foreign keys to this Relation
     * @return Column[]
     */
    public function getForeignKeys() {
        $result = array();
        $keys = array();
        if (isset($this->keysByType['FOREIGN KEY'])) {
            $keys = array_merge($keys, $this->keysByType['FOREIGN KEY']);
        }

        foreach ($keys as $key) {
            $result[$key] = $this->keys[$key];
        }
        return $result;
    }

    /**
     * Gets the keys local to this Relation
     * @return Column[]
     */
    public function getLocalKeys() {
        $result = array();
        $keys = array();
        foreach (array('PRIMARY KEY', 'UNIQUE', 'VIEW') as $type) {
            if (isset($this->keysByType[$type])) {
                $keys = array_merge($keys, $this->keysByType[$type]);
            }
        }

        foreach ($keys as $key) {
            $result[$key] = $this->keys[$key];
        }
        return $result;
    }

    /**
     * Adds a Column to the list of columns
     * @param \Blend\Component\Database\Schema\Column $column
     * @throws InvalidSchemaException
     */
    public function addColumn(Column $column) {
        $name = $column->getName();
        if (!isset($this->columns[$name])) {
            $this->columns[$name] = $column;
        } else {

            Throw new InvalidSchemaException("Column {$column} already exists in {$this->getName()}");
        }
    }

    /**
     * Adds key column to the list of keys
     * @param type $keyColumn
     * @param type $constraint_type
     */
    public function addKeyColumn($keyColumn, $constraint_type) {
        $name = $keyColumn['constraint_name'];
        if (stripos($name, '_pkey') !== false) {
            $name = $constraint_type;
        }
        $this->keysByType[$constraint_type][] = $name;
        $this->keys[$name][] = $this->columns[$keyColumn['column_name']];
    }

}
