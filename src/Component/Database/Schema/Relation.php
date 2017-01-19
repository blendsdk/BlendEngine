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
 * Represents a relation from the database. The relation is either a
 * table of a view.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Relation extends Record
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var Column[]
     */
    private $columns;

    /**
     * @var Constraint[]
     */
    private $constraints;

    public function __construct(array $data, Schema $schema)
    {
        parent::__construct($data);
        $this->schema = $schema;
        $this->columns = array();
        $this->constraints = array();
    }

    /**
     * Get the table Schema.
     *
     * @return Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get the table name.
     *
     * @return type
     */
    public function getName()
    {
        return $this->getValue('table_name');
    }

    /**
     * Adds a Column to this Relation.
     *
     * @param Column $column
     */
    public function addColumn(Column $column)
    {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * Gets a column by name.
     *
     * @param string $name
     *
     * @return Column
     */
    public function getColumn($name)
    {
        return $this->columns[$name];
    }

    /**
     * Get the columns of this relation.
     *
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Adds a constraint the this Relation.
     *
     * @param Constraint $constraint
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[$constraint->getName()] = $constraint;
    }

    /**
     * Gets the constraints.
     *
     * @return Constraint[]
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Get a constraint by name.
     *
     * @param string $name
     *
     * @return Constraint
     */
    public function getConstraint($name)
    {
        return $this->constraints[$name];
    }
}
