<?php

/*
 *  This file is part of the BlendEngine framework.
 *
 *  (c) Gevik Babakhani <gevikb@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Blend\Component\Database\Schema;

/**
 * Represents a database schema with relations.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Schema extends Record
{
    /**
     * @var Relation[]
     */
    private $relations;

    const NAME_FIELD = 'schema_name';

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->relations = array();
    }

    /**
     * Get the schema name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getValue(self::NAME_FIELD);
    }

    /**
     * Get the relations of this schema.
     *
     * @return Relation[]
     */
    public function getRelations()
    {
        return $this->relations;
    }

    public function addRelation(Relation $relation)
    {
        $this->relations[$relation->getName()] = $relation;
    }

    /**
     * Gets a relation of name.
     *
     * @param type $name
     *
     * @return Relation
     */
    public function getRelation($name)
    {
        return $this->relations[$name];
    }
}
