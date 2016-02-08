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

use Blend\DataModelBuilder\Schema\Record;
use Blend\DataModelBuilder\Schema\Relation;
use Blend\Component\Exception\InvalidSchemaException;

/**
 * Schema represents a schema from a PostgreSQL database
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Schema extends Record {

    /**
     * @var Relation[] 
     */
    private $relations = [];

    /**
     * Gets the schema name
     * @return string
     */
    public function getName($prettify = false) {
        if ($prettify) {
            return str_identifier($this->record['schema_name']);
        } else {
            return $this->record['schema_name'];
        }
    }

    /**
     * Retusn the list of relations
     * @return Relation[]
     */
    public function getRelations() {
        return $this->relations;
    }

    /**
     * Adds Relation to the list of relations in this Schema
     * @param Relation $relation
     * @throws InvalidSchemaException
     */
    public function addRelation(Relation $relation) {
        $name = $relation->getName();
        if (!isset($this->relations[$name])) {
            $this->relations[$name] = $relation;
        } else {
            throw new InvalidSchemaException("Relation {$name} already exists in {$this->getName()} schema!");
        }
    }

}
