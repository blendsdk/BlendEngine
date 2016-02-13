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
use Blend\Component\DI\Container;
use Blend\Component\Database\Factory\Converter\FieldConverter;
use Blend\Component\Model\Model;
use Blend\Component\Database\StatementResult;
use Blend\Component\Exception\InvalidConfigException;

/**
 * Factory is the base class for a model factory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Factory {

    /**
     * The name of the relation/view to operate
     * @var string
     */
    protected $relation;

    /**
     * Reference to a Database object
     * @var Database
     */
    protected $database;

    /**
     * Name of the Model Class that is used to convert the database records to
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

    public function __construct(Database $database, $modelClass) {
        $this->database = $database;
        $this->modelClass = $modelClass;
        $this->container = new Container();
        $this->container->define('model', [
            'class' => $modelClass
        ]);
        $this->fieldConverter = null;
    }

    /**
     * Saved a Model in the database either by inserting or updating
     * @param Model $model The Model to save
     */
    public function save(Model $model) {
        if ($model->isNew()) {
            $this->insertModel($model);
        } else {
            $this->updateModel($model);
        }
    }
    

    
    /**
     * Updates an existing Model (record) in the database and updates
     * the Model with the data return from the database. If the current
     * state of the Model is already changed by another operation then this
     * method with throw a
     * @param Model $model
     * @throws InvalidConfigException
     */
    protected function updateModel(Model $model) {
        $stmtResult = new StatementResult();
        $condition = sqlstr('');
        $conditionParameters = [];
        $first = true;
        foreach ($model->getInitial() as $field => $value) {
            if (!$first) {
                $condition->append('AND', true, true);
            }
            $condition->append($field);
            if (is_null($value)) {
                $condition->isNull();
            } else {
                $param = ':up_' . $field;
                $condition->equalsTo($param);
                $conditionParameters[$param] = $value;
            }
            $first = false;
        }

        $result = $this->database->update($this->relation
                , $model->getUpdates()
                , $condition, $conditionParameters
                , $stmtResult);

        if ($stmtResult->getAffectedRecords() === 1) {
            $this->syncModel($model, $result[0]);
        } else {
            throw new \LogicException(
            "The update operation did not return exactly one record! "
            . "The Model must have been changed by another operation!");
        }
    }

    /**
     * Inserts a new Model into the database and updates the Model with
     * the data return from the database
     * @param Model $model
     * @throws InvalidConfigException
     */
    protected function insertModel(Model $model) {
        $stmtResult = new StatementResult();
        $result = $this->database->insert(
                $this->relation
                , $this->convertFromModel($model->getData())
                , $stmtResult
        );
        if ($stmtResult->getAffectedRecords() === 1) {
            $this->syncModel($model, $result[0]);
        } else {
            throw new \LogicException(
            "The insert operation did not return exactly one record!");
        }
    }

    /**
     * Synchronizes model data with values returned from the Database
     * @param Model $model
     * @param array $record
     * @return model
     */
    protected function syncModel(Model $model, $record) {
        $model->sync($this->convertFromRecord($record));
    }

    /**
     * Creates a new end empty instance of the registered modelClass
     * @return Model
     */
    public function newModel() {
        return $this->container->get('model');
    }

    /**
     * Converts from a Model to a Record format
     * @param array $data
     * @return array
     */
    protected function convertFromModel(array $data) {
        return $data;
    }

    /**
     * Converts from a record to Model format
     * @param array $record
     * @return array
     */
    protected function convertFromRecord(array $record) {
        return $record;
    }

}
