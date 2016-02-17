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

use Blend\DataModelBuilder\Builder\ClassBuilder;
use Blend\DataModelBuilder\Schema\Relation;

/**
 * Description of SchemaBuilder
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class SchemaBuilder extends ClassBuilder {

    public function __construct(Relation $relation, $includeSchema) {
        parent::__construct('schema', $relation, $includeSchema);
    }

    /**
     * Sets the root namespace where the models will be generated
     * @param type $schema
     */
    public function setRootNamespace($schema) {
        parent::setRootNamespace($schema);
        $this->rootNamespace .= '\\Schema';
    }

    protected function createBuildDefinition($allowCustomize) {
        return array(
            array(
                'className' => strtoupper($this->relation->getName() . '_SCHEMA'),
                'classNamespace' => $this->rootNamespace,
                'classBaseClass' => $this->defaultBaseClassName,
                'uses' => [$this->defaultBaseClassFQN],
                'generate' => true
            )
        );
    }

    protected function preparBuildDefinition($def) {
        $properties = [];
        foreach ($this->relation->getColumns() as $column) {
            $name = $column->getName();
            $dbtype = $column->getField('data_type');
            $properties[] = array(
                'name' => strtoupper($name),
                'column' => $name,
                'type' => $column->getField('data_type')
            );
        }
        $def['props'] = $properties;
        return $def;
    }

}
