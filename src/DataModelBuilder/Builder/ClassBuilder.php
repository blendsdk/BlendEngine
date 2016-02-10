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

use Blend\DataModelBuilder\Schema\Relation;
use Blend\Component\Filesystem\Filesystem;

/**
 * Description of ClassBuilder
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
abstract class ClassBuilder {

    protected $template;
    protected $includeSchema;
    protected $rootNamespace;
    protected $applicationNamespace;
    protected $rootPath;
    protected $classNamePostfix;
    protected $defaultBaseClassName;
    protected $defaultBaseClassFQN;
    protected $fileSystem;

    /**
     * @var Relation 
     */
    protected $relation;

    protected abstract function preparBuildDefinition($def);

    public function __construct($template, Relation $relation, $includeSchema) {
        $this->template = dirname(__FILE__) . "/Template/{$template}.php";
        $this->relation = $relation;
        $this->includeSchema = $includeSchema;
        $this->classNamePostfix = '';
        $this->fileSystem = new Filesystem();
    }

    public function setApplicationNamespace($namespace) {
        $this->applicationNamespace = $namespace;
    }

    public function setRootNamespace($namespace) {
        $this->rootNamespace = $namespace
                . ($this->includeSchema ? '\\' . $this->relation->getSchemaName(true) : '');
    }

    public function setRootPath($path) {
        $this->rootPath = $path;
    }

    protected function createBuildDefinition($allowCustomize) {

        $className = $this->relation->getName(true) . $this->classNamePostfix;

        if ($allowCustomize) {
            $classes = array(
                array(
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace,
                    'classBaseClass' => $className . 'Base',
                    'uses' => [$this->applicationNamespace . '\\' . $this->rootNamespace . '\\Base\\' . $className . ' as ' . $className . 'Base']
                ),
                array(
                    'classModifier' => 'abstract',
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace . '\\Base',
                    'classBaseClass' => $this->defaultBaseClassName,
                    'uses' => [$this->defaultBaseClassFQN],
                    'generate' => true
                )
            );
        } else {
            $classes = array(
                array(
                    'className' => $className,
                    'classNamespace' => $this->rootNamespace,
                    'classBaseClass' => $this->defaultBaseClassName,
                    'uses' => [$this->defaultBaseClassFQN],
                    'generate' => true
                )
            );
        }

        return $classes;
    }

    public function build($allowCustomize) {
        $classes = $this->createBuildDefinition($allowCustomize);
        foreach ($classes as $def) {
            $targetFolder = $this->createTargetFolder($def);
            $targetFile = "{$targetFolder}/{$def['className']}.php";
            $def['appNamespace'] = $this->applicationNamespace;
            $def['classFQRN'] = $this->relation->getFQRN();
            render_php_template($this->template, $this->preparBuildDefinition($def), $targetFile, false);
        }
    }

    protected function createTargetFolder($def) {
        $path = $this->normalizePath($this->rootPath . '/' . $def['classNamespace']);
        if (!$this->fileSystem->exists($path)) {
            $this->fileSystem->mkdir($path);
        }
        return $path;
    }

    protected function normalizePath($str) {
        return str_replace('\\', '/', $str);
    }

}
