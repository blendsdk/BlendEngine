<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Builder\Config;

use Blend\Component\Filesystem\Filesystem;

/**
 * ModelBuilderConfig base class for a ModelBuilder Configuration.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class BuilderConfig
{
    const MODEL_FACTORY_RETURN_SINGLE = 's';
    const MODEL_FACTORY_RETURN_MULTIPLE = 'm';

    protected $projectFolder;
    protected $targetRootFolder;
    protected $modelFactoryMethods;

    /**
     * Should return either a null meaning all or an array with FQRN of the
     * relations for which a Schema object needs to be generated.
     */
    abstract public function getSchemaHelperListToGenerate();

    /**
     * Should return the root namespace of your application.
     */
    abstract public function getApplicationNamespace();

    /**
     * Should resturn the root namespace of your DAL (Data Access Layer)
     * "Database" for example.
     */
    abstract public function getModelRootNamespace();

    /**
     * Should return a string array of schemas to generate otherwise it should
     * return "null" to generate all the schemas.
     */
    abstract public function getSchemaListToGenerate();

    /**
     * Should return a string array of relation (tables and views) names that
     * you are going to customize.
     */
    abstract public function getCustomizedRelationList();

    /**
     * Should return a converter identifier based on the fully qualified
     * column name, thatis schema.relation.column (public.userser.user_name).
     */
    abstract public function getConverterForField($schema, $relation, $column, $dbtype, $fqcn);

    /**
     * Should return a FQCN string of your FieldConverter class.
     */
    abstract public function getFieldConverterClass();

    /**
     * Should return the local date format, for example
     * return [
     *      'date' => 'd-m-Y',
     *      'time' => 'H:i:s',
     *      'datetime' => 'd-m-Y H:i:s'
     * ].
     */
    abstract public function getLocalDateTimeFormat();

    /**
     * Can optionally be used to register custom getter for a given relation
     * This is usefull to create factory methods for data VIEWS.
     */
    protected function registerModelFactoryMethods()
    {
        return null;
    }

    /**
     * Adds (registers) a factory method definition to be generated by the
     * factory builder for agiven relation.
     *
     * @param string       $relation
     * @param string/array $columns
     * @param string       $type
     */
    protected function addModelFactoryMethod($relation, $columns, $type = self::MODEL_FACTORY_RETURN_MULTIPLE)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if (stripos($relation, '.') === false) {
            $relation = 'public.'.$relation;
        }

        if (!isset($this->modelFactoryMethods[$relation])) {
            $this->modelFactoryMethods[$relation] = [];
        }
        $this->modelFactoryMethods[$relation][] = [
            'columns' => $columns,
            'type' => $type,
        ];
    }

    /**
     * Get the list of registered model factory methods.
     *
     * @return array
     */
    public function getModelFactoryMethods()
    {
        return $this->modelFactoryMethods;
    }

    public function __construct($projectFolder)
    {
        $this->projectFolder = $projectFolder;
        $this->targetRootFolder = $projectFolder.'/src';
        $this->modelFactoryMethods = [];
        $fs = new Filesystem();
        $fs->ensureFolder($this->targetRootFolder);
        $this->registerModelFactoryMethods();
    }

    /**
     * Gets the root folder where DAL files will be generated.
     *
     * @return type
     */
    public function getTargetRootFolder()
    {
        return $this->targetRootFolder;
    }
}
