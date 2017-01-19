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
 * Represents a Constraint from a Relation in a Database.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Constraint extends Record
{
    /**
     * @var Column[]
     */
    private $columns;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }

    public function __construcst($name)
    {
        $this->columns = array();
    }

    /**
     * Gets the constraint name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getValue('constraint_name');
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
}
