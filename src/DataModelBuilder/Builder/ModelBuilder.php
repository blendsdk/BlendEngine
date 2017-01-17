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

/**
 * ModelBuilder builds a Model class for a given Relation (table/view).
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class ModelBuilder extends ClassBuilder
{
    public function __construct(Relation $relation, $includeSchema)
    {
        parent::__construct('model', $relation, $includeSchema);
        $this->defaultBaseClassName = 'Model';
        $this->defaultBaseClassFQN = 'Blend\Component\Model\Model';
    }

    /**
     * Sets the root namespace where the models will be generated.
     *
     * @param type $schema
     */
    public function setRootNamespace($schema)
    {
        parent::setRootNamespace($schema);
        $this->rootNamespace .= '\\Model';
    }

    /**
     * Here we loop the columns and create a property definition.
     * We also resolve an optional field converter for a given property
     * that we provided in the builder configuration file.
     *
     * @param array $def
     *
     * @return type
     */
    protected function preparBuildDefinition($def)
    {
        $properties = array();
        foreach ($this->relation->getColumns() as $column) {
            $name = $column->getName();
            $type = 'mixed';
            $schema = $column->getField('table_schema');
            $relation = $column->getField('table_name');
            $dbtype = $column->getField('data_type');
            $this->resolveColumnConverter($schema, $relation, $column->getName(), $dbtype, $column->getFQCN());
            $properties[] = array(
                'name' => $name,
                'getter' => 'get'.str_identifier(strtolower($name)),
                'setter' => 'set'.str_identifier(strtolower($name)),
                'type' => $type,
            );
        }
        $def['props'] = $properties;

        return $def;
    }
}
