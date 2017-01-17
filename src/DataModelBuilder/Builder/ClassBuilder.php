<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Builder;

use Blend\Component\Filesystem\Filesystem;
use Blend\DataModelBuilder\Schema\Relation;

/**
 * Description of ClassBuilder.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ClassBuilder
{
    protected $template;
    protected $includeSchema;
    protected $rootNamespace;
    protected $applicationNamespace;
    protected $rootPath;
    protected $classNamePostfix;
    protected $defaultBaseClassName;
    protected $defaultBaseClassFQN;
    protected $fileSystem;
    protected $columnConverterResolver;
    protected $fieldConverterInfo;
    protected $fieldConverterClass;
    protected $fieldConverterClassParams;

    /**
     * @var Relation
     */
    protected $relation;

    abstract protected function preparBuildDefinition($def);

    public function __construct($template, Relation $relation, $includeSchema)
    {
        $this->template = dirname(__FILE__)."/Template/{$template}.php";
        $this->relation = $relation;
        $this->includeSchema = $includeSchema;
        $this->classNamePostfix = '';
        $this->fileSystem = new Filesystem();
        $this->fieldConverterInfo = [];
        $this->fieldConverterClass = null;
        $this->fieldConverterClassParams = null;
    }

    /**
     * Sets the FQCN of the FieldConverter class.
     *
     * @param type $class
     */
    public function setFieldConverterClass($class)
    {
        $this->fieldConverterClass = $class;
    }

    /**
     * Gets the previously set the FieldConverter class.
     *
     * @return type
     */
    public function getFieldConverterInfo()
    {
        return $this->fieldConverterInfo;
    }

    /**
     * Set the previously generated field converter information.
     *
     * @param type $info
     */
    public function setFieldConverterInfo($info)
    {
        $this->fieldConverterInfo = $info;
    }

    /**
     * Sets the columnConverterResolver. This is a closure that will call the
     * config->getConverterForField(....).
     *
     * @param \Blend\DataModelBuilder\Builder\callable $resolver
     */
    public function setColumnConverterResolver(callable $resolver)
    {
        $this->columnConverterResolver = $resolver;
    }

    /**
     * Setter to the Application's root namespace.
     *
     * @param type $namespace
     */
    public function setApplicationNamespace($namespace)
    {
        $this->applicationNamespace = $namespace;
    }

    /**
     * Setter for the rootNamespace. For example (MyApp)\Database[Model/Factory].
     *
     * @param type $namespace
     */
    public function setRootNamespace($namespace)
    {
        $this->rootNamespace = $namespace
                .($this->includeSchema ? '\\'.$this->relation->getSchemaName(true) : '');
    }

    /**
     * Sets the root path where the files will be generated. (.../src).
     *
     * @param type $path
     */
    public function setRootPath($path)
    {
        $this->rootPath = $path;
    }

    /**
     * Here we use the columnConverterResolver (closure set from the DataModelCommand)
     * to optionally get a converter for a column.
     *
     * @param type $schema
     * @param type $relation
     * @param type $column
     * @param type $dbtype
     * @param type $fqcn
     */
    public function resolveColumnConverter($schema, $relation, $column, $dbtype, $fqcn)
    {
        if ($this->columnConverterResolver) {
            $converter = call_user_func_array($this->columnConverterResolver, [$schema, $relation, $column, $dbtype, $fqcn]);
            if (!is_null($converter)) {
                if (!is_array($converter)) {
                    $converter = [$converter];
                }
                if (is_array($converter)) {
                    $this->fieldConverterInfo[$column] = $converter;
                }
            }
        }
    }

    /**
     * Here we create a build definition (what to generate) based on the
     * $allowCustomize flag. If a class needs to be able to customized then
     * we create a bass class and generate the code in the base class,
     * and create derived class from the base class. Otherwise we create just
     * one class and generate the code in that class.
     *
     * @param type $allowCustomize
     *
     * @return array
     */
    protected function createBuildDefinition($allowCustomize)
    {
        $className = $this->relation->getName(true).$this->classNamePostfix;

        if ($allowCustomize) {
            $classes = array(
                array(
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace,
                    'classBaseClass' => $className.'Base',
                    'uses' => [$this->applicationNamespace.'\\'.$this->rootNamespace.'\\Base\\'.$className.' as '.$className.'Base'],
                ),
                array(
                    'classModifier' => 'abstract',
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace.'\\Base',
                    'classBaseClass' => $this->defaultBaseClassName,
                    'uses' => [$this->defaultBaseClassFQN],
                    'generate' => true,
                ),
            );
        } else {
            $classes = array(
                array(
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace,
                    'classBaseClass' => $this->defaultBaseClassName,
                    'uses' => [$this->defaultBaseClassFQN],
                    'generate' => true,
                ),
            );
        }

        return $classes;
    }

    /**
     * Builds one or more classes based on the build definition.
     *
     * @param bool $allowCustomize
     */
    public function build($allowCustomize)
    {
        $classes = $this->createBuildDefinition($allowCustomize);
        foreach ($classes as $def) {
            $targetFolder = $this->createTargetFolder($def);
            $targetFile = "{$targetFolder}/{$def['className']}.php";
            $def['appNamespace'] = $this->applicationNamespace;
            $def['classFQRN'] = $this->relation->getFQRN();
            render_php_template($this->template, $this->preparBuildDefinition($def), $targetFile, false);
        }
    }

    /**
     * Creates a target folder for a class definition.
     *
     * @param type $def
     *
     * @return type
     */
    protected function createTargetFolder($def)
    {
        $path = $this->normalizePath($this->rootPath.'/'.$def['classNamespace']);
        if (!$this->fileSystem->exists($path)) {
            $this->fileSystem->mkdir($path);
        }

        return $path;
    }

    /**
     * Normalizer to convert the \ to / when used namespace parts as path parts.
     *
     * @param type $str
     *
     * @return string
     */
    protected function normalizePath($str)
    {
        return str_replace('\\', '/', $str);
    }
}
