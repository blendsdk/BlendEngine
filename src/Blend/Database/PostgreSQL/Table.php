<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Database\PostgreSQL;

use Blend\Database\PostgreSQL\Column;
use Blend\Database\PostgreSQL\Record;

/**
 * Description of Table
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Table extends Record {

    protected $columns;
    protected $keys;
    protected $keysByType;
    protected $schema_namespace;
    protected $model_base_namespace;
    protected $model_namespace;
    protected $service_base_namespace;
    protected $service_namespace;

    public function __construct($record = array()) {
        parent::__construct($record);
        $this->columns = array();
        $this->keys = array();
        $this->keysByType = array();
    }

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

    public function getLocalKeys() {
        $result = array();
        $keys = array();
        if (isset($this->keysByType['PRIMARY KEY'])) {
            $keys = array_merge($keys, $this->keysByType['PRIMARY KEY']);
        }

        if (isset($this->keysByType['UNIQUE'])) {
            $keys = array_merge($keys, $this->keysByType['UNIQUE']);
        }

        foreach ($keys as $key) {
            $result[$key] = $this->keys[$key];
        }
        return $result;
    }

    public function getKeyQueryParams($keyname) {
        $list = array();
        foreach ($this->keys[$keyname] as $column) {
            $name = $column->getColumnName();
            $list[] = 'SC::' . strtoupper($name) . ' => $' . $name;
        }
        return implode(', ', $list);
    }

    public function getKeyGetterArgs($keyname) {
        $list = array();
        foreach ($this->keys[$keyname] as $column) {
            $list[] = '$' . $column->getColumnName();
        }
        return implode(', ', $list);
    }

    public function getKeyFunctionName($keyname, $prefix) {
        $list = array();
        foreach ($this->keys[$keyname] as $column) {
            $list[] = $column->getColumnName();
        }
        return $prefix . $this->ucWords(implode(' And ', $list));
    }

    public function getServiceClassName() {
        return $this->getClassName(true) . 'Service';
    }

    public function getServiceNamespace() {
        return $this->service_namespace;
    }

    public function setServiceNamespace($namespace) {
        $this->service_namespace = $namespace;
    }

    public function getServiceBaseNamespace() {
        return $this->service_base_namespace;
    }

    public function setServiceBaseNamespace($namespace) {
        $this->service_base_namespace = $namespace;
    }

    public function getModelNamespace() {
        return $this->model_namespace;
    }

    public function setModelNamespace($namespace) {
        $this->model_namespace = $namespace;
    }

    public function getModelBaseNamespace() {
        return $this->model_base_namespace;
    }

    public function setModelBaseNamespace($namespace) {
        $this->model_base_namespace = $namespace;
    }

    public function setSchemaNamespace($namespace) {
        $this->schema_namespace = $namespace;
    }

    public function getModelClassName() {
        return $this->ucWords($this->getTableName());
    }

    public function getSchemaNamespace() {
        return $this->schema_namespace;
    }

    public function getSchemaClassName() {
        return $this->getTableName(true) . '_SCHEMA';
    }

    public function getClassName() {
        return $this->ucWords($this->getTableName());
    }

    public function getColumns() {
        return $this->columns;
    }

    public function addKeyColumn(array $keyColumn, $constraint_type) {
        $name = $keyColumn['constraint_name'];
        if (stripos($name, '_pkey') !== false) {
            $name = 'primary';
        }
        $this->keysByType[$constraint_type][] = $name;
        $this->keys[$name][] = $this->columns[$keyColumn['column_name']];
    }

    public function addColumn(Column $column) {
        $this->columns[$column->getColumnName()] = $column;
    }

    public function getTableName($uppercase = false) {
        if ($uppercase) {
            return strtoupper($this->data['table_name']);
        } else {
            return $this->data['table_name'];
        }
    }

    public function getTableCatalog() {
        return $this->data['table_catalog'];
    }

    public function getTableSchema() {
        return $this->data['table_schema'];
    }

}
