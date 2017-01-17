<?php

/*
 * This file is part of the BlendEngine framework.
 *
 * (c) Gevik Babakhani <gevikb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Blend\DataModelBuilder\Schema;

use Blend\Component\Exception\InvalidSchemaException;

/**
 * Schema represents a schema from a PostgreSQL database.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Schema extends Record
{
    /**
     * @var Relation[]
     */
    private $relations = array();

    /**
     * Gets if this is the only available schema in the database.
     *
     * @return type
     */
    public function isSingle()
    {
        return $this->record['is_single'];
    }

    public function getName($prettify = false)
    {
        $name = $this->getString('schema_name');
        if ($prettify) {
            if ($name == 'public') {
                $name = 'common';
            }

            return str_identifier($name);
        } else {
            return $this->getString('schema_name');
        }
    }

    /**
     * Retusn the list of relations.
     *
     * @return Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Adds Relation to the list of relations in this Schema.
     *
     * @param Relation $relation
     *
     * @throws InvalidSchemaException
     */
    public function addRelation(Relation $relation)
    {
        $name = $relation->getName();
        if (!isset($this->relations[$name])) {
            $this->relations[$name] = $relation;
        } else {
            throw new InvalidSchemaException("Relation {$name} already exists in {$this->getName()} schema!");
        }
    }
}
