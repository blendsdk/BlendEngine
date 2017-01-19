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

    public function __construct(array $data)
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
}
