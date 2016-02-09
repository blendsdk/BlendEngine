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
 * Description of ModelBuilder
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ModelBuilder extends ClassBuilder {

    public function __construct(Relation $relation, $includeSchema) {
        parent::__construct('model', $relation, $includeSchema);
        $this->defaultBaseClassName = 'Model';
        $this->defaultBaseClassFQN = 'Blend\Component\Model\Model';
    }

    public function setRootNamespace($schema) {
        parent::setRootNamespace($schema);
        $this->rootNamespace .= '\\Model';
    }

    protected function preparBuildDefinition($def) {
        $properties = [];
        foreach ($this->relation->getColumns() as $column) {
            $name = $column->getName();
            $type = 'mixed';
            $properties[] = array(
                'name' => $name,
                'getter' => 'get' . str_identifier($name),
                'setter' => 'set' . str_identifier($name),
                'type' => $type
            );
        }
        $def['props'] = $properties;
        return $def;
    }

}