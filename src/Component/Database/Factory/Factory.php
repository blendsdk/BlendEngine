<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\Component\Database\Factory;

use Blend\Component\Database\Database;
use Blend\Component\Database\Factory\Converter\FieldConverter;
use Blend\Component\Database\StatementResult;
use Blend\Component\DI\Container;
use Blend\Component\Exception\DatabaseQueryException;
use Blend\Component\Exception\InvalidConfigException;
use Blend\Component\Model\Model;

/**
 * Factory is the base class for a model factory.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Factory
{
    const ALL_COLUMNS = '*';

    /**
     * The name of the relation/view to operate.
     *
     * @var string
     */
    protected $relation;

    /**
     * Reference to a Database object.
     *
     * @var Database
     */
    protected $database;

    /**
     * Name of the Model Class that is used to convert the database records to.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var FieldConverter
     */
    protected $fieldConverter;

    public function __construct(Database $database, $modelClass)
    {
        $this->database = $database;
        $this->modelClass = $modelClass;
        $this->container = new Container();
        $this->container->defineClassWithInterface('model', $modelClass);
        $this->fieldConverter = null;
    }

    /**
     * Saved a Model in the database either by inserting or updating.
     *
     * @param Model $model The Model to save
     */
    public function save(Model $model)
    {
        if ($model->isNew()) {
            $this->insertModel($model);
        } else {
            $this->updateModel($model);
        }
    }

    /**
     * Deletes a Model from the database.
     *
     * @param Model $model The Model that was deleted
     */
    public function delete(Model $model)
    {
        if (!$model->isNew()) {
            $stmtResult = new StatementResult();

            list($condition, $conditionParameters) = $this->createAndCondition(
                    $model->getInitial()
            );

            $result = $this->database->delete($this->relation, $condition, $conditionParameters, $stmtResult);

            if ($stmtResult->getAffectedRecords() === 1) {
                $this->syncModel($model, $result[0]);
            } else {
                throw new \LogicException(
                'The delete operation did not return exactly one record! '
                .'The Model must have been changed or deleted by another operation!');
            }
        } else {
            throw new \LogicException('Unable to delete an unsaved model!');
        }
    }

    /**
     * Updates an existing Model (record) in the database and updates
     * the Model with the data return from the database. If the current
     * state of the Model is already changed by another operation then this
     * method with throw a.
     *
     * @param Model $model
     *
     * @throws InvalidConfigException
     */
    protected function updateModel(Model $model)
    {
        $stmtResult = new StatementResult();

        list($condition, $conditionParameters) = $this->createAndCondition(
                $model->getInitial()
        );

        $result = $this->database->update($this->relation, $model->getUpdates(), $condition, $conditionParameters, $stmtResult);

        if ($stmtResult->getAffectedRecords() === 1) {
            $this->syncModel($model, $result[0]);
        } else {
            throw new \LogicException(
            'The update operation did not return exactly one record! '
            .'The Model must have been changed or deleted by another operation!');
        }
    }

    /**
     * Inserts a new Model into the database and updates the Model with
     * the data return from the database.
     *
     * @param Model $model
     *
     * @throws InvalidConfigException
     */
    protected function insertModel(Model $model)
    {
        $stmtResult = new StatementResult();
        $result = $this->database->insert(
                $this->relation, $this->convertFromModel($model->getData()), $stmtResult
        );
        if ($stmtResult->getAffectedRecords() === 1) {
            $this->syncModel($model, $result[0]);
        } else {
            throw new \LogicException(
            'The insert operation did not return exactly one record!');
        }
    }

    /**
     * Synchronizes model data with values returned from the Database.
     *
     * @param Model $model
     * @param array $record
     *
     * @return model
     */
    protected function syncModel(Model $model, $record)
    {
        $model->sync($this->convertFromRecord($record));
    }

    /**
     * Creates a new end empty instance of the registered modelClass.
     *
     * @return Model
     */
    public function newModel()
    {
        return $this->container->get('model');
    }

    /**
     * Converts from a Model to a Record format.
     *
     * @param array $data
     *
     * @return array
     */
    protected function convertFromModel(array $data)
    {
        return $data;
    }

    /**
     * Converts from a record to Model format.
     *
     * @param array $record
     *
     * @return array
     */
    protected function convertFromRecord(array $record)
    {
        return $record;
    }

    /**
     * Creates an AND condition from an associative array.
     *
     * @param array $params
     *
     * @return array
     */
    protected function createAndCondition(array $params)
    {
        $condition = sqlstr('');
        $conditionParameters = array();
        $first = true;
        foreach ($params as $field => $value) {
            if (!$first) {
                $condition->append(' AND ');
            }
            $condition->append($field);
            if (is_null($value)) {
                $condition->isNull();
            } else {
                $param = ':cc_'.$field;
                $condition->equalsTo($param);
                $conditionParameters[$param] = $value;
            }
            $first = false;
        }

        return array($condition, $conditionParameters);
    }

    /**
     * Gets all records from a relation providing an option to
     * limit and order the results.
     *
     * @param array $selectColumns
     * @param type  $orderDirective
     * @param type  $offsetLimitDirective
     *
     * @return type
     */
    public function getAll($selectColumns, $orderDirective = null, $offsetLimitDirective = null)
    {
        return $this->getManyBy(
                        $selectColumns, array('true' => true), $orderDirective, $offsetLimitDirective);
    }

    /**
     * Gets a single records using $byColumns as AND condition.
     *
     * @param array $selectColumns
     * @param array $byColumns
     *
     * @return type
     */
    protected function getOneBy(array $selectColumns, array $byColumns)
    {
        list($condition, $conditionParams) = $this->createAndCondition($byColumns);
        $sql = 'SELECT '
                .implode(', ', $selectColumns)
                .' FROM '.$this->relation
                .' WHERE '.$condition;

        $result = $this->database->executeQuery($sql, $conditionParams);
        if (count($result) !== 0) {
            return $this->container->get('model', array('data' => $this->convertFromRecord($result[0])));
        } else {
            return null;
        }
    }

    /**
     * Deletes a single record by $byColumns as condition and arguments.
     *
     * @param array $byColumns
     */
    protected function deleteOneBy(array $byColumns)
    {
        $stmtResult = new StatementResult();
        list($condition, $conditionParams) = $this->createAndCondition($byColumns);
        $result = $this->database->delete($this->relation, $condition, $conditionParams, $stmtResult);
        if ($stmtResult->getAffectedRecords() == 1) {
            return $this->container->get('model', array('data' => $this->convertFromRecord($result[0])));
        } elseif ($stmtResult->getAffectedRecords() > 1) {
            throw new DatabaseQueryException('The '.__FUNCTION__
            .' deleted more than one record!');
        } else {
            return null;
        }
    }

    /**
     * Deletes records by $byColumns as condition and arguments.
     *
     * @param array $byColumns
     */
    protected function deleteManyBy(array $byColumns, StatementResult $stmtResult = null)
    {
        list($condition, $conditionParams) = $this->createAndCondition($byColumns);
        $result = $this->database->delete($this->relation, $condition, $conditionParams, $stmtResult);

        return $this->recordsToModels($result);
    }

    /**
     * Returns the count of record by a condition set in $byColumns.
     *
     * @param array $byCondition
     *
     * @return int
     */
    protected function countBy(array $byCondition)
    {
        list($condition, $conditionParams) = $this->createAndCondition($byCondition);
        $sql = 'SELECT COUNT(true)'
                .' FROM '.$this->relation
                .' WHERE '.$condition;

        return $this->database->executeScalar($sql, $conditionParams);
    }

    /**
     * Retuns the count of all records in the relation.
     *
     * @return int
     */
    protected function countAll()
    {
        $sql = 'SELECT COUNT(true) FROM '.$this->relation;

        return $this->database->executeScalar($sql);
    }

    /**
     * Gets many records from a relation.
     *
     * @param array $selectColumns        column o select
     * @param array $byColumns            condition
     * @param type  $orderDirective
     * @param type  $offsetLimitDirective
     *
     * @return array
     */
    protected function getManyBy($selectColumns, array $byColumns, $orderDirective = null, $offsetLimitDirective = null)
    {
        if (is_null($selectColumns)) {
            $selectColumns = array(self::ALL_COLUMNS);
        } elseif (!is_array($selectColumns)) {
            $selectColumns = array($selectColumns);
        }

        list($condition, $conditionParams) = $this->createAndCondition($byColumns);
        $sql = 'SELECT '
                .implode(', ', $selectColumns)
                .' FROM '.$this->relation
                .' WHERE '.$condition
                .$this->createOrderDirective($orderDirective)
                .$this->createOffsetLimitDirective($offsetLimitDirective);

        $result = $this->database->executeQuery($sql, $conditionParams);

        return $this->recordsToModels($result);
    }

    /**
     * Converts records to models.
     *
     * @param array $records array of records
     *
     * @return array array of models
     */
    protected function recordsToModels(&$records)
    {
        if (is_array($records) && count($records) !== 0) {
            foreach ($records as $key => $record) {
                $records[$key] = $this->container->get('model', array('data' => $this->convertFromRecord($record)));
            }

            return $records;
        } else {
            return $records;
        }
    }

    /**
     * Creates an OFFSET ## LIMIT ## directive to be appended to s SQL query.
     *
     * @param array $offsetLimitDirective
     *
     * @return string
     */
    protected function createOffsetLimitDirective($offsetLimitDirective)
    {
        $sql = '';
        if (is_array($offsetLimitDirective)) {
            foreach (array('limit', 'offset') as $directive) {
                if (isset($offsetLimitDirective[$directive])) {
                    $sql .= ' '
                            .strtoupper($directive)
                            .' '.$offsetLimitDirective[$directive];
                }
            }
        }

        return $sql;
    }

    /**
     * Creates an ORDER BY section to be appended to a SQL query.
     *
     * @param array $orderDirective
     *
     * @return string
     */
    protected function createOrderDirective($orderDirective)
    {
        $sql = '';
        if (is_array($orderDirective) && count($orderDirective) !== 0) {
            foreach ($orderDirective as $col => $orderType) {
                $orderDirective[$col] = $col.' '.$orderType;
            }
            $sql .= ' ORDER BY '.implode(', ', $orderDirective);
        }

        return $sql;
    }
}
