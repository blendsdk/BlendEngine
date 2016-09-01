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
    protected $customFactoryMethods;

    public function __construct(Relation $relation, $includeSchema) {
        parent::__construct('factory', $relation, $includeSchema);
        $this->defaultBaseClassName = $this->classNamePostfix = 'Factory';
        $this->defaultBaseClassFQN = 'Blend\Component\Database\Factory\Factory';
    }

    /**
     * Sets the custom factory methods list
     * @param type $methods
     */
    public function setCustomFactoryMethods($methods) {
        $this->customFactoryMethods = $methods;
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
        $this->prepareCustomFactoryMethods();
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
        $def['uniqueKeys'] = array_merge(
                $this->createDefinitionsForKeys($this->relation->getUniqueKeys())
                , $this->createDefinitionsForKeys($this->relation->getCustomKeys('CUSTOM_SINGLE'))
        );
        $def['multiKeys'] = array_merge(
                $this->createDefinitionsForKeys($this->relation->getForeignKeys())
                , $this->createDefinitionsForKeys($this->relation->getCustomKeys('CUSTOM_MULTI'))
        );
        $def['is_writable'] = $this->relation->writable();

        return $def;
    }

    protected function prepareCustomFactoryMethods() {
        $relName = $this->relation->getFQRN();
        if (isset($this->customFactoryMethods[$relName])) {
            foreach ($this->customFactoryMethods[$relName] as $methodDef) {
                $constraint_name = md5(serialize($methodDef));
                foreach ($methodDef['columns'] as $column) {
                    $keyColumn = [
                        'constraint_name' => $constraint_name,
                        'column_name' => $column
                    ];
                    $this->relation->addKeyColumn($keyColumn, 'CUSTOM_' . ($methodDef['type'] === 's' ? 'SINGLE' : 'MULTI'));
                }
            }
        }
    }

    protected function createDefinitionsForKeys($list) {
        $result = [];
        if (is_array($list) && count($list) !== 0) {
            foreach ($list as $columns) {
                $result[] = $this->createCallerDefinition($columns);
            }
        }
        return $result;
    }

    /**
     * Creates a caller definition based on an array with columns to be used
     * to generate function parameters
     * @param array $columns
     * @return array
     */
    private function createCallerDefinition(array $columns) {
        $functionName = [];
        $functionParams = [];
        $functionCallParam = [];
        foreach ($columns as $column) {
            $argName = '$' . strtolower($column->getName());
            $colName = $column->getName();
            /* @var $column \Blend\DataModelBuilder\Schema\Column */
            $functionName[] = str_identifier(str_replace('_id', '_ID', $colName));
            $functionParams[] = $argName;
            $functionParamsDoc[] = [$column->getField('udt_name'), $argName];
            $functionCallParam[] = "'$colName' => " . $argName;
        }
        return [
            'functionName' => ucwords(implode('And', $functionName)),
            'functionParams' => implode(', ', $functionParams),
            'functionParamsDoc' => $functionParamsDoc,
            'functionCallParam' => implode(', ', $functionCallParam)
        ];
    }

}
