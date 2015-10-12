<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Data;

use Monolog\Logger;

/**
 * Encapsulates common database functionality. This class is available as
 * a service from the Blend\Core\Application
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Database extends \PDO {

    /**
     * @var \Monolog\Logger
     */
    private $logger;

    /**
     * @var boolean;
     */
    private $debug;

    /**
     * @var string
     */
    private $database_name;

    /**
     * @param array $config
     * @param Logger $logger
     * @param boolean $debug
     */
    public function __construct($config, Logger $logger = null, $debug = false) {
        $this->logger = $logger;
        $this->debug = $debug;
        $this->database_name = $config['database'];
        $dsn = "pgsql:host={$config['host']};dbname={$this->database_name};port={$config['port']}";
        parent::__construct($dsn, $config['username'], $config['password']);
    }

    /**
     * Retrives the name of the current database
     * @return string
     */
    public function getDatabaseName() {
        return $this->database_name;
    }

    /**
     * Executes a SQL query script
     * @param type $sql
     * @return boolean
     * @throws DatabaseQueryException
     */
    public function executeScript($sql) {
        $this->debug($sql);
        $result = $this->exec($sql);
        if (intval($this->errorCode()) !== 0) {
            $exception = DatabaseQueryException::createFromStatement($this);
            $this->logError($exception->getMessage());
            throw $exception;
        } else {
            return $result;
        }
    }

    /**
     * Executes a SQL query and returns a recordset as an associative array
     * @param string $sql
     * @param array $params
     * @return array
     * @throws DatabaseQueryException
     */
    public function executeQuery($sql, $params = array()) {
        $statement = $this->prepare($sql);
        $statement->execute($params);
        $this->debug($sql, $params);

        if (intval($statement->errorCode()) === 0) {
            return $statement->fetchAll(self::FETCH_ASSOC);
        } else {
            $exception = DatabaseQueryException::createFromStatement($statement);
            $this->logError($exception->getMessage(), array(
                'arguments' => $params
            ));
            throw $exception;
        }
    }

    /**
     * Inserts data into a table using an associative array
     * @param string $table_name
     * @param array $parameters
     * @return array[][] recordset
     */
    public function insert($table_name, $parameters) {
        $values = array();
        $fields_names = implode(', ', array_keys($parameters));
        foreach ($parameters as $field => $value) {
            $values[$this->query_param($field)] = $this->parseValue($value);
        }
        $place_holders = implode(', ', array_keys($values));
        $sql = "INSERT INTO {$table_name} ({$fields_names}) values({$place_holders}) RETURNING *";
        return $this->executeQuery($sql, $values);
    }

    /**
     * Updates a table using an associative array
     * and optionally a where clause
     * @param string $table_name
     * @param array $values
     * @param string $clause
     * @param arrsy $clause_parameters
     * @return array[][] recordset
     */
    public function update($table_name, $values, $clause, $clause_parameters = array()) {
        $field_names = array();
        $field_values = array();
        $where_clause = '';
        foreach ($values as $field => $value) {
            $param = $this->query_param($field);
            $field_names[] = "{$field} = {$param}";
            $field_values[$param] = $this->parseValue($value);
        }
        $place_holders = implode(', ', $field_names);
        if (!empty($clause)) {
            $where_clause = " WHERE {$clause} ";
            $field_values = array_merge($field_values, $clause_parameters);
        }
        $sql = "UPDATE {$table_name} SET {$place_holders} $where_clause  RETURNING *";
        return $this->executeQuery($sql, $field_values);
    }

    /**
     * Deletes records using an associative array
     * @param string $table_name
     * @param string $calue
     * @param array $clause_parameters
     * @return array[][] recordset
     */
    public function delete($table_name, $calue, $clause_parameters = array()) {
        $where_clause = '';
        if (!empty($calue)) {
            $where_clause = " WHERE {$calue} ";
        }
        $sql = "DELETE FROM {$table_name} {$where_clause} RETURNING *";
        return $this->executeQuery($sql, $clause_parameters);
    }

    /**
     * Parses the PHP values to a corresponding database fromat
     * @param mixed $value
     * @return mixed
     */
    private function parseValue($value) {
        if (is_bool($value)) {
            return $value === true ? 'true' : 'false';
        } else if (is_null($value)) {
            return 'null';
        } else {
            return $value;
        }
    }

    /**
     * Creates a prepared statement parameter
     * @param string $name
     * @return string
     */
    private function query_param($name) {
        return ":{$name}";
    }

    /**
     * Logs a debug message if a logger is provided
     * @param string $message
     * @param array $context
     */
    private function debug($message, $context = array()) {
        if ($this->debug === true && !is_null($this->logger)) {
            $this->logger->debug($message, $context);
        }
    }

    /**
     * Loggs an error message if a logger is provided
     * @param string $message
     * @param array $context
     */
    private function logError($message, $context = array()) {
        if (!is_null($this->logger)) {
            $this->logger->error($message, $context);
        }
    }

}
