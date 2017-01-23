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
 * Represents a record from the database.
 *
 * @author Gevik Babakhani <gevikb@gmail.com>
 */
class Record
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Returns the value of a column from this record.
     *
     * @param type $column
     *
     * @return mixed
     */
    public function getValue($column)
    {
        return $this->data[$column];
    }

    /**
     * Gets the data collection from this Record
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Sets a value of a field in the collection
     * @param string $name
     * @param mixed $value
     */
    public function setValue($name, $value)
    {
        $this->data[$name] = $value;
    }
}
