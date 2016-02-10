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
use Blend\Component\Database\Factory\TypeConverter;
use Blend\Component\Database\Factory\DefaultTypeConverter;

/**
 * Factory is the base class for a model factory
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class Factory {

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
     * @var TypeConverter
     */
    protected $typeConverter;

    public function __construct(Database $database, $modelClass, TypeConverter $typeConverter = null) {
        $this->database = $database;
        $this->modelClass = $modelClass;
        $this->container = new Container();
        $this->container->define('model');
        $this->typeConverter = is_null($typeConverter) ?
                $this->container->get(DefaultTypeConverter::class) : $typeConverter;
    }

    protected function createNewModel(array $record = []) {
        return $this->container->get('model', [
                    'record' => $this->convertFromRecord($record)
        ]);
    }

    protected function convertFromRecord(array $record) {
        return $record;
    }

}
