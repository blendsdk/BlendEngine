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

use Blend\Database\Database;
use Blend\Database\Model;
use Blend\Database\QueryResult;
use Blend\Database\InsertStatementException;
use Blend\Database\UpdateStatementException;
use Blend\Database\DeleteStatementException;

/**
 * DatabaseSerice is an abstract class that can be used as a Data Access Layer
 * avoifing to use the Database object directly
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class DatabaseService {

    /**
     * @var Database
     */
    protected $database;

    public function __construct(Database $database) {
        $this->database = $database;
    }

    /**
     * Saves an object Model in the database
     * @param string $table_name
     * @param Model $object
     * @return Model
     */
    protected function saveObject($table_name, Model &$object) {
        if ($object->isUnSaved()) {
            return $object->loadRecord($this->insertRecord($table_name, $object->getData()));
        } else {
            return $object->loadRecord($this->updateRecord($table_name, $object->getData(), $object->getInitial(), 1));
        }
    }

    protected function deleteObject($table_name, Model $object) {
        if (!$object->isUnSaved()) {
            $this->deleteByParams($table_name, $object->getInitial(), 1);
            return $object;
        } else {
            throw new DeleteStatementException("Unable to delete an unsaved (in memory) record model");
        }
    }

    /**
     * Updates a recrod in the database
     * @param string $table_name
     * @param array $fields
     * @param array $criteria
     * @return array
     */
    protected function updateRecord($table_name, $fields, $criteria, $expectedRecords = null) {
        if (isset($criteria['date_changed'])) {
            $fields['date_changed'] = date('c');
        }
        $args = $this->makeUPDATEArgs('p_', $criteria);
        $sql = "UPDATE {$table_name} SET {$this->makeSetParams($fields, ', ')} WHERE {$this->makeSetParams($args, ' AND ', true, $criteria)} RETURNING *";
        $params = array_merge($fields, $this->renameArrayKeys('p_', array_remove_nulls($criteria)));
        $result = $this->database->executeQuery($sql, $this->makeQueryParams($params));
        if (is_array($result)) {
            if (is_null($expectedRecords)) {
                return $result;
            } else if (count($result) === $expectedRecords) {
                return $expectedRecords === 1 ? $result[0] : $result;
            } else {
                $errorMessage = "Invalid number of records updated. Excepcted: {$expectedRecords}, updated:" . count($result);
                $this->database->debug($errorMessage, $result);
                throw new UpdateStatementException($errorMessage);
            }
        } else {
            $errorMessage = "The result set is an invalid recordset!";
            $this->database->debug($errorMessage, $result);
            throw new UpdateStatementException($errorMessage);
        }
    }

    /**
     * Inserts a record into the database
     * @param string $table_name
     * @param array $params
     * @return array the newly created record from the database
     */
    protected function insertRecord($table_name, $params = array()) {
        $fields = implode(', ', array_keys($params));
        $sets = implode(', ', array_keys($this->renameArrayKeys(':', $params)));
        $sql = "INSERT INTO {$table_name} ($fields) values ({$sets}) RETURNING *";
        $result = $this->database->executeQuery($sql, $this->makeQueryParams($params));
        if (is_array($result) && count($result) === 1) {
            return $result[0];
        } else {
            $errorMessage = "The result set is an invalid recordset!";
            $this->database->debug($errorMessage, $result);
            throw new InsertStatementException($errorMessage);
        }
    }

    /**
     * @param string $table_name
     * @param mixed $params
     * @param string $classType
     * @return Model
     */
    protected function getObjectByParams($table_name, $params, $recordClass) {
        $sql = "SELECT * FROM {$table_name} WHERE {$this->makeSetParams($params)}";
        $result = $this->database->executeQuery($sql, $this->makeQueryParams($params));
        if (is_array($result) && count($result) === 1) {
            return new $recordClass($result[0]);
        } else {
            return null;
        }
    }

    /**
     * Retrives all records
     * @param string $table_name
     * @param string $recordClass
     * @param \Blend\Database\callable $handler
     * @return Model[]
     */
    protected function getAllObjects($table_name, $recordClass, callable $handler = null) {
        $sql = "SELECT * FROM {$table_name}";
        $result = $this->database->executeQuery($sql);
        if (is_array($result)) {
            return $this->convertRecordSetToObjectSet($result, $recordClass, $handler);
        } else {
            return array();
        }
    }

    /**
     * @param string $table_name
     * @param mixed $params
     * @param string $classType
     * @return Model[]
     */
    protected function getManyObjectsByParams($table_name, $params, $recordClass, callable $handler = null) {
        $sql = "SELECT * FROM {$table_name} WHERE {$this->makeSetParams($params)}";
        $result = $this->database->executeQuery($sql, $this->makeQueryParams($params));
        if (is_array($result)) {
            return $this->convertRecordSetToObjectSet($result, $recordClass, $handler);
        } else {
            return array();
        }
    }

    protected function convertRecordSetToObjectSet($recordSet, $recordClass, callable $handler = null) {
        $set = array();
        foreach ($recordSet as $record) {
            $object = new $recordClass($record);
            if (is_null($handler)) {
                $set[] = $object;
            } else {
                $set[] = call_user_func($handler, $object);
            }
        }
        return $set;
    }

    /**
     * @param string $table_name
     * @param moxed $params
     * @param string $classType
     * @return Model
     */
    protected function deleteByParams($table_name, $params, $expectedRecords = null) {
        $sql = "DELETE FROM {$table_name} WHERE {$this->makeSetParams($params)} RETURNING *";
        $queryResult = new QueryResult();
        $result = $this->database->executeQuery($sql, $this->makeQueryParams($params), $queryResult);
        if (is_array($result)) {
            if (is_null($expectedRecords)) {
                return $result;
            } else if ($queryResult->getAffectedRecords() === $expectedRecords) {
                return $expectedRecords === 1 ? $result[0] : $result;
            } else {
                $errorMessage = "Invalid number of records deleted. Excepcted: {$expectedRecords}, deleted:" . count($result);
                $this->database->debug($errorMessage, $result);
                throw new DeleteStatementException($errorMessage);
            }
        } else {
            $errorMessage = "The result set is an invalid recordset!";
            $this->database->debug($errorMessage, $result);
            throw new DeleteStatementException($errorMessage);
        }
    }

    /**
     * Creates the arguments for an update clause
     * @param string $prefix
     * @param array $data
     * @return array
     */
    protected function makeUPDATEArgs($prefix, $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$k] = "{$prefix}$k";
        }
        return $result;
    }

    /**
     * Renames the keys of an array
     * @param string $prefix
     * @param data $data
     * @return array
     */
    protected function renameArrayKeys($prefix, $data) {
        $result = array();
        foreach ($data as $k => $v) {
            $result[$prefix . $k] = $v;
        }
        return $result;
    }

    /**
     * Create query parameters to be used in a executeQuery call
     * @param array $list
     * @return array
     */
    protected function makeQueryParams($list) {
        $result = array();
        foreach ($list as $key => $value) {
            $k = ":{$key}";
            $result[$k] = $value;
        }
        return $result;
    }

    /**
     * Create field = :field set seperated by a $glue.
     * @param array $list
     * @param string $glue
     * @param boolean $useValue
     * @return string
     */
    protected function makeSetParams(array $list, $glue = ' AND ', $useValue = false, $criteria = null) {
        $result = array();
        foreach ($list as $key => $value) {
            if ($useValue) {
                if (is_array($criteria) && array_key_exists($key, $criteria) && is_null($criteria[$key])) {
                    $result[] = "{$key} IS NULL";
                } else {
                    $result[] = "{$key} = :{$value}";
                }
            } else {
                $result[] = "{$key} = :{$key}";
            }
        }
        return implode($glue, $result);
    }

}
