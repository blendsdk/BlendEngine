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
 * FactoryBuilder builds a factory class for a given relation (table/view)
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class FactoryBuilder extends ClassBuilder {

    protected $modelRootNamespace;

    public function __construct(Relation $relation, $includeSchema) {
        parent::__construct('factory', $relation, $includeSchema);
        $this->defaultBaseClassName = $this->classNamePostfix = 'Factory';
        $this->defaultBaseClassFQN = 'Blend\Component\Database\Factory\Factory';
    }

    /**
     * Sets the root namespace where the factory classes will be generated
     * @param type $schema
     */
    public function setRootNamespace($schema) {
        parent::setRootNamespace($schema);
        $this->modelRootNamespace = $this->rootNamespace;
        $this->rootNamespace .= '\\Factory';
    }

    /**
     * Here we configure the build definitions by adding the "use" statements
     * and setting the paremeters for a fieldConverter if needed
     * @param type $def
     * @return type
     */
    protected function preparBuildDefinition($def) {
        $modelClass = $this->applicationNamespace
                . '\\' . $this->modelRootNamespace
                . '\\Model\\' . $this->relation->getName(true);
        $def['modelClass'] = $modelClass;
        $def['uses'] = [
            'Blend\Component\Database\Database',
            'Blend\Component\Database\Factory\Factory',
            $modelClass
        ];
        if (count($this->fieldConverterInfo) !== 0) {
            $def['uses'][] = $this->fieldConverterClass;
            $ns = explode('\\', $modelClass);
            $def['uses'][] = "{$ns[0]}\\{$ns[1]}\\DateTimeConversion";
            $class = explode('\\', $this->fieldConverterClass);
            $def['fieldConverter'] = end($class);
        }
        $def['converters'] = $this->fieldConverterInfo;

        return $def;
    }

}
