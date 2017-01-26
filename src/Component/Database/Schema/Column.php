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
 * Represents a Column in a Relation of a database.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Column extends Record
{
    /**
     * @var Relation
     */
    private $relation;

    public function __construct(array $data, Relation $relation)
    {
        parent::__construct($data);
        $this->relation = $relation;
    }

    /**
     * Get the name of this Column.
     *
     * @return string
     */
    public function getName()
    {
        return $this->getValue('column_name');
    }

    /**
     * Gets the Relation of this Column.
     *
     * @return Relation
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Gets the data type of this column.
     *
     * @return string
     */
    public function getType()
    {
        return $this->getValue('data_type');
    }
}
